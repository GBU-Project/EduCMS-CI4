<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    protected SettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        $data = [
            'title'       => 'Setelan Website',
            'breadcrumbs' => ['Setelan Website' => ''],
        ];

        $this->_ensureDefaultSettings();

        $allSettings = [];
        $db = \Config\Database::connect();

        if ($db->tableExists('settings')) {
            $rows = $this->settingModel->findAll();
            foreach ($rows as $row) {
                $allSettings[$row->group_name][$row->key] = $row;
            }
        }

        $data['all_settings'] = $allSettings;

        return view('admin/settings/index', $data);
    }

    public function save()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url('admin/settings'));
        }

        $settings = $this->request->getPost('settings');
        $warnings = [];
        $db       = \Config\Database::connect();

        if (! empty($settings) && is_array($settings) && $db->tableExists('settings')) {
            foreach ($settings as $group => $keys) {
                if (is_array($keys)) {
                    foreach ($keys as $key => $value) {
                        $key   = (string) str_replace(':', '.', $key);
                        $value = is_string($value) ? trim($value) : $value;

                        // Email validation
                        if (in_array($key, ['email', 'site_email', 'smtp_user'], true) && ! empty($value)) {
                            if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $warnings[] = 'Format email pada key (' . $key . ') tidak valid.';
                            }
                        }

                        // Social & Website URL validation
                        if ((in_array($key, ['facebook', 'instagram', 'youtube', 'twitter', 'tiktok', 'site_url'], true) || strpos($key, 'url') !== false) && ! empty($value)) {
                            if (! preg_match('#^(https?://|//|@)#i', $value)) {
                                if (strpos($value, '.') !== false && strpos($value, ' ') === false) {
                                    $value = 'https://' . $value;
                                }
                            }
                        }

                        // Google Maps Embed & URL normalization
                        if ($key === 'maps_embed' && ! empty($value)) {
                            $srcUrl = null;

                            if (preg_match('#<iframe[^>]+src=["\']([^"\']+)["\']#i', $value, $matches)) {
                                $srcUrl = $matches[1];
                            } elseif (preg_match('#^https?://#i', $value)) {
                                $srcUrl = $value;
                            }

                            if ($srcUrl !== null) {
                                if ($this->_isAllowedMapsEmbedUrl($srcUrl)) {
                                    $value = '<iframe src="' . htmlspecialchars($srcUrl, ENT_QUOTES) . '" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" sandbox="allow-scripts allow-same-origin"></iframe>';
                                } else {
                                    $value      = '';
                                    $warnings[] = 'URL Google Maps Embed ditolak: hanya domain google.com/maps yang diperbolehkan.';
                                }
                            } else {
                                $value      = '';
                                $warnings[] = 'Format Google Maps Embed tidak dikenali dan telah dikosongkan demi keamanan.';
                            }
                        }

                        $existing = $db->table('settings')->where(['group_name' => $group, 'key' => $key])->get()->getRow();
                        if ($existing) {
                            $db->table('settings')->where(['group_name' => $group, 'key' => $key])
                                ->update(['value' => $value, 'updated_at' => date('Y-m-d H:i:s')]);
                        } else {
                            $db->table('settings')->insert([
                                'group_name'  => $group,
                                'key'         => $key,
                                'value'       => $value,
                                'is_autoload' => 1,
                                'created_at'  => date('Y-m-d H:i:s'),
                                'updated_at'  => date('Y-m-d H:i:s'),
                            ]);
                        }
                    }
                }
            }
        }

        $this->logActivity('Settings', 'save', null, 'Settings updated');

        if (! empty($warnings)) {
            session()->setFlashdata('warning', 'Setelan disimpan dengan peringatan: ' . implode(' ', $warnings));
        } else {
            session()->setFlashdata('success', 'Setelan berhasil disimpan.');
        }

        return redirect()->to(base_url('admin/settings'));
    }

    private function _ensureDefaultSettings()
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('settings')) {
            return;
        }

        $defaults = [
            'school' => [
                'welcome_speech'     => ['value' => '', 'is_autoload' => 1],
                'principal_photo'    => ['value' => '', 'is_autoload' => 1],
                'vision'             => ['value' => '', 'is_autoload' => 1],
                'mission'            => ['value' => '', 'is_autoload' => 1],
                'wa_default_message' => ['value' => 'Halo Admin Sekolah, saya ingin bertanya informasi seputar sekolah.', 'is_autoload' => 1],
                'maps_embed'         => ['value' => '', 'is_autoload' => 1],
            ],
            'seo' => [
                'og_image' => ['value' => '', 'is_autoload' => 1],
            ],
            'homepage' => [
                'hero.order'          => ['value' => '10', 'is_autoload' => 1],
                'welcome.order'       => ['value' => '20', 'is_autoload' => 1],
                'vision.order'        => ['value' => '30', 'is_autoload' => 1],
                'stats.order'         => ['value' => '40', 'is_autoload' => 1],
                'programs.order'      => ['value' => '50', 'is_autoload' => 1],
                'news.order'          => ['value' => '60', 'is_autoload' => 1],
                'announcements.order' => ['value' => '70', 'is_autoload' => 1],
                'agenda.order'        => ['value' => '80', 'is_autoload' => 1],
                'videos.order'        => ['value' => '90', 'is_autoload' => 1],
                'gallery.order'       => ['value' => '100', 'is_autoload' => 1],
                'partners.order'      => ['value' => '110', 'is_autoload' => 1],
                'testimonials.order'  => ['value' => '120', 'is_autoload' => 1],
                'ppdb.order'          => ['value' => '130', 'is_autoload' => 1],
                'cta.order'           => ['value' => '140', 'is_autoload' => 1],
                'welcome.enabled'     => ['value' => '1', 'is_autoload' => 1],
                'welcome.title'       => ['value' => 'Sambutan Kepala Sekolah', 'is_autoload' => 1],
                'welcome.subtitle'    => ['value' => '', 'is_autoload' => 1],
                'vision.enabled'      => ['value' => '1', 'is_autoload' => 1],
                'vision.title'        => ['value' => 'Visi & Misi Sekolah', 'is_autoload' => 1],
                'vision.subtitle'     => ['value' => '', 'is_autoload' => 1],
                'cta.enabled'         => ['value' => '1', 'is_autoload' => 1],
                'cta.title'           => ['value' => 'Penerimaan Peserta Didik Baru (PPDB)', 'is_autoload' => 1],
                'cta.subtitle'        => ['value' => '', 'is_autoload' => 1],
            ],
        ];

        foreach ($defaults as $group => $keys) {
            foreach ($keys as $key => $config) {
                $existing = $db->table('settings')->where([
                    'group_name' => $group,
                    'key'        => $key,
                ])->get()->getRow();

                if (! $existing) {
                    $db->table('settings')->insert([
                        'group_name'  => $group,
                        'key'         => $key,
                        'value'       => $config['value'],
                        'is_autoload' => $config['is_autoload'],
                        'created_at'  => date('Y-m-d H:i:s'),
                        'updated_at'  => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }

    private function _isAllowedMapsEmbedUrl($url): bool
    {
        $parts = parse_url(trim($url));
        if ($parts === false || empty($parts['host']) || empty($parts['scheme'])) {
            return false;
        }

        if (! in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return false;
        }

        $host         = strtolower($parts['host']);
        $allowedHosts = [
            'google.com', 'www.google.com',
            'maps.google.com',
        ];

        foreach ($allowedHosts as $allowed) {
            if ($host === $allowed) {
                return true;
            }
        }

        if (preg_match('#^(www\.)?google\.[a-z.]{2,10}$#i', $host)) {
            return true;
        }

        return false;
    }

    protected function logActivity(string $module, string $action, ?string $oldValue = null, ?string $newValue = null)
    {
        $userId = session()->get('user_id');
        if (class_exists('\Logger')) {
            $logger = new \Logger();
            $logger->log($userId, $module, $action, $oldValue, $newValue);
        }
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Theme_website extends BaseController
{
    protected function availableThemes(): array
    {
        return [
            'default' => [
                'name'        => 'EduCMS Default',
                'description' => 'Tampilan modern, bersih, dan responsif. Cocok digunakan oleh sebagian besar sekolah.',
                'status'      => 'available',
            ],
            'islamic' => [
                'name'        => 'EduCMS Islamic',
                'description' => 'Tampilan bernuansa Islami yang dirancang untuk sekolah Islam, madrasah, dan pesantren.',
                'status'      => 'available',
            ],
        ];
    }

    public function index()
    {
        helper(['educms']);

        $data = [
            'title'        => 'Theme Website',
            'breadcrumbs'  => ['Pengaturan Sistem' => '', 'Theme Website' => ''],
            'themes'       => $this->availableThemes(),
            'active_theme' => get_setting('theme', 'active_theme', 'default'),
        ];

        return view('admin/theme_website/index', $data);
    }

    public function save()
    {
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return redirect()->to(base_url('admin/theme-website'));
        }

        $requested = (string) $this->request->getPost('active_theme');
        $themes    = $this->availableThemes();

        if (! isset($themes[$requested]) || $themes[$requested]['status'] !== 'available') {
            session()->setFlashdata('error', 'Template yang dipilih belum tersedia.');
            return redirect()->to(base_url('admin/theme-website'));
        }

        $db = \Config\Database::connect();
        if ($db->tableExists('settings')) {
            $existing = $db->table('settings')->where(['group_name' => 'theme', 'key' => 'active_theme'])->get()->getRow();
            if ($existing) {
                $db->table('settings')->where(['group_name' => 'theme', 'key' => 'active_theme'])
                    ->update(['value' => $requested, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $db->table('settings')->insert([
                    'group_name'  => 'theme',
                    'key'         => 'active_theme',
                    'value'       => $requested,
                    'is_autoload' => 1,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->logActivity('Theme Website', 'save', null, 'active_theme = ' . $requested);
        session()->setFlashdata('success', 'Template website berhasil disimpan.');
        return redirect()->to(base_url('admin/theme-website'));
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

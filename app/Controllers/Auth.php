<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        /** @var \App\Libraries\AuthNative $auth */
        $auth = service('auth');

        if ($auth->is_logged_in()) {
            return redirect()->to(base_url('admin'));
        }

        if ($this->request->getMethod() === 'post') {
            $identity = $this->request->getPost('identity');
            $password = $this->request->getPost('password');
            $remember = (bool) $this->request->getPost('remember');

            if (empty($identity) || empty($password)) {
                session()->setFlashdata('error', 'Username/Email dan Password wajib diisi.');
                return view('auth/login');
            }

            $loginResult = $auth->login($identity, $password, $remember);

            if ($loginResult === 'DEFAULT_CREDENTIALS') {
                session()->setFlashdata('error', 'Password default belum diubah demi keamanan. Silakan ubah password terlebih dahulu.');
                return view('auth/login');
            }

            if ($loginResult) {
                $redirectTo = session()->get('redirect_to');
                session()->remove('redirect_to');

                if ($redirectTo && trim($redirectTo, '/') !== 'admin/login') {
                    return redirect()->to(base_url($redirectTo));
                }

                return redirect()->to(base_url('admin'));
            }

            session()->setFlashdata('error', 'Username/Email atau Password salah.');
            return redirect()->to(base_url('admin/login'));
        }

        return view('auth/login');
    }

    public function logout()
    {
        /** @var \App\Libraries\AuthNative $auth */
        $auth = service('auth');
        $auth->logout();

        return redirect()->to(base_url('admin/login'));
    }

    public function forgot()
    {
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');

            if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                session()->setFlashdata('error', 'Alamat email tidak valid.');
                return view('auth/forgot');
            }

            $db     = \Config\Database::connect();
            $exists = false;

            if ($db->tableExists('users')) {
                $row    = $db->table('users')->where('email', $email)->where('deleted_at', null)->get()->getRow();
                $exists = (bool) $row;
            } else {
                $exists = ($email === 'admin@educms.local');
            }

            if ($exists) {
                session()->setFlashdata('success', 'Instruksi pemulihan sandi telah dikirim ke email Anda.');
            } else {
                session()->setFlashdata('error', 'Email tidak ditemukan dalam sistem kami.');
            }

            return redirect()->to(base_url('admin/forgot'));
        }

        return view('auth/forgot');
    }
}

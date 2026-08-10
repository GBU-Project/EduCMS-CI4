<?php

namespace App\Libraries;

use App\Models\UserModel;

class AuthNative
{
    const DEFAULT_SEEDED_PASSWORD_HASH = '$2y$10$YgYchpmroT8E7W7r65xTWeY75pXkkIRlyAXdkgKu6m6z/0lD05uvu';

    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function is_logged_in(): bool
    {
        return session()->has('user_id') && session()->get('user_id') > 0;
    }

    public function login(string $identity, string $password, bool $remember = false)
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('users')) {
            return false;
        }

        $user = $db->table('users')
            ->groupStart()
                ->where('username', $identity)
                ->orWhere('email', $identity)
            ->groupEnd()
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->get()
            ->getRow();

        if ($user && password_verify($password, $user->password)) {
            if (hash_equals(self::DEFAULT_SEEDED_PASSWORD_HASH, $user->password)) {
                return 'DEFAULT_CREDENTIALS';
            }

            $sessionData = [
                'user_id'   => $user->id,
                'username'  => $user->username,
                'full_name' => $user->full_name,
                'email'     => $user->email,
                'logged_in' => true,
            ];

            session()->set($sessionData);
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        session()->destroy();
    }

    public function user_id(): ?int
    {
        return session()->get('user_id') ? (int) session()->get('user_id') : null;
    }
}

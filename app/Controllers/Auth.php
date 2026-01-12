<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $username = trim($this->request->getPost('username'));
            $password = trim($this->request->getPost('password'));

            // Verify User
            $user = $this->userModel->where('username', $username)->first();

            if ($user && password_verify($password, $user['password'])) {
                $this->setUserSession($user);
                $msg = ($user['is_master']) ? 'Selamat Datang, Owner!' : 'Login berhasil!';
                return redirect()->to('/dashboard')->with('success', $msg);
            } else {
                return redirect()->back()->with('error', 'Username atau password salah.');
            }
        }

        return view('auth/login');
    }

    private function setUserSession($user)
    {
        $sessionData = [
            'user_id' => $user['id'],
            'nama' => $user['nama'],
            'username' => $user['username'],
            'role' => $user['role'],
            'foto' => $user['foto'],
            'is_master' => $user['is_master'],
            'isLoggedIn' => true
        ];
        session()->set($sessionData);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Logout berhasil!');
    }
}
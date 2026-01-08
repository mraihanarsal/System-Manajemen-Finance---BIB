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

            // 1. Check Master Credentials
            $isMasterInput = ($username === 'Rickylidya' && $password === '12345678');

            if ($isMasterInput) {
                // Try to find by username OR ID 1
                $user = $this->userModel->where('username', 'Rickylidya')->first();
                if (!$user) {
                    $user = $this->userModel->find(1);
                }

                // If still no user, create it (Self-Healing)
                if (!$user) {
                    $this->userModel->insert([
                        'id' => 1,
                        'nama' => 'Rickylidya',
                        'username' => 'Rickylidya',
                        'password' => password_hash('12345678', PASSWORD_DEFAULT),
                        'role' => 'admin',
                        'foto' => 'undraw_profile_2.svg',
                        'is_master' => 1,
                        'status' => 'active'
                    ]);
                    $user = $this->userModel->find(1);
                } else {
                    // Update master status if exists
                    if ($user['username'] !== 'Rickylidya' || !$user['is_master']) {
                         $this->userModel->update($user['id'], [
                             'username' => 'Rickylidya', // Force reset username to match master creds
                             'is_master' => 1
                         ]);
                         $user = $this->userModel->find($user['id']);
                    }
                }
                
                // Force Login
                $this->setUserSession($user);
                return redirect()->to('/dashboard')->with('success', 'Selamat Datang, Owner!');
            }

            // 2. Regular Login
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
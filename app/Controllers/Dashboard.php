<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $userModel;

   

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        
        $data = [
            'title' => 'Dashboard',
            'user' => $this->getUserData()
        ];
        return view('dashboard/index', $data);
    }
    
public function profile()
{
    
    
    $userData = $this->getUserData();
    $data = [
        'title' => 'Profile',
        'user' => [
            'id' => $userData['id'],
            'nama' => $userData['nama'],
            'username' => $userData['username'] ?? $userData['nama'], // Tambahkan username
            'email' => $userData['email'],
            'role' => $userData['role'],
            'foto' => $userData['foto'],
            'is_master' => $userData['is_master'],
            'status' => 'active'
        ]
    ];
    return view('dashboard/profile', $data);
}

    public function update_profile()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $userId = session()->get('user_id');
        $nama = $this->request->getPost('nama');
        $username = $this->request->getPost('username');
        
        // Validation Rules
        $rules = [
            'nama' => 'required|min_length[3]',
            'username' => "required|min_length[3]|is_unique[users.username,id,{$userId}]" // Unique check except current user
        ];

        if (!$this->validate($rules)) {
            // Get first error message
            $errors = $this->validator->getErrors();
            $msg = reset($errors);
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        try {
            // Update database
            $this->userModel->update($userId, [
                'nama' => $nama,
                'username' => $username
            ]);
            
            // Update session
            session()->set('nama', $nama);
            session()->set('username', $username);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Profile berhasil diperbarui']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
        }
    }

public function upload_foto()
{
    if (!session()->get('isLoggedIn')) {
        return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
    }

    $file = $this->request->getFile('foto');
    
    if ($file && $file->isValid() && !$file->hasMoved()) {
        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya file gambar yang diizinkan (JPEG, PNG, GIF)']);
        }
        
        // Validasi ukuran file (max 2MB)
        if ($file->getSize() > 2097152) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 2MB']);
        }
        
        $newName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads/profiles', $newName);
        
        // Update database
        $this->userModel->update(session()->get('user_id'), [
            'foto' => $newName
        ]);
        
        // Update session
        session()->set('foto', $newName);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Foto berhasil diupload']);
    }
    
    return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid']);
}

public function ganti_password()
{
    if (!session()->get('isLoggedIn')) {
        return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
    }

    $password = $this->request->getPost('password');
    
    if (strlen($password) < 6) {
        return $this->response->setJSON(['success' => false, 'message' => 'Password minimal 6 karakter']);
    }
    
    // Update password di database
    $this->userModel->update(session()->get('user_id'), [
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ]);
    
    return $this->response->setJSON(['success' => true, 'message' => 'Password berhasil diubah']);
}
    // UBAH: _kelola_pengguna MENJADI kelola_pengguna
    public function kelola_pengguna()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        if (!session()->get('is_master') && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
        $data = [
            'title' => 'Kelola Pengguna',
            'users' => $this->userModel->getUsersExceptMaster(),
            'user' => $this->getUserData()
        ];
        return view('dashboard/kelola_pengguna', $data); // Pastikan view juga tanpa underscore
    }

    // UBAH: _login_logout MENJADI login_logout
    public function login_logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }

    private function getUserData()
    {
        if (session()->get('isLoggedIn')) {
            return [
                'id' => session()->get('user_id'),
                'nama' => session()->get('nama'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
                'foto' => session()->get('foto'),
                'is_master' => session()->get('is_master')
            ];
        }
        // return $this->userModel->first();
    }
}
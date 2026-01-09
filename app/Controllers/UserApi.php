<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class UserApi extends ResourceController
{
    use ResponseTrait;

    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    // GET /api/users
    public function getAll()
    {
        $users = $this->model->getUsersExceptMaster();
        return $this->respond($users);
    }

    // POST /api/users
    public function create()
    {
        $rules = [
            'nama'     => 'required|min_length[3]',
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,user]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'nama'     => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
            'role'     => $this->request->getVar('role'),
            'status'   => 'active',
            'foto'     => 'undraw_profile_2.svg'
        ];

        // Handle Photo Upload
        $file = $this->request->getFile('foto');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validation (manual check or rule? Manual is often easier in controller for optional files)
            if (strpos($file->getMimeType(), 'image/') === 0 && $file->getSize() <= 2097152) {
                 $newName = $file->getRandomName();
                 $file->move(ROOTPATH . 'public/uploads/profiles', $newName);
                 $data['foto'] = $newName;
            }
        }

        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 'success', 'message' => 'User berhasil ditambahkan']);
        }

        return $this->failServerError('Gagal menambahkan user');
    }

    // PUT /api/users/(:num)
    public function update($id = null)
    {
        if (!$id) return $this->failNotFound('ID User tidak ditemukan');

        $user = $this->model->find($id);
        if (!$user) return $this->failNotFound('User tidak ditemukan');

        $rules = [
            'nama'     => 'required|min_length[3]',
            'username' => 'required|min_length[3]|is_unique[users.username,id,'.$id.']',
            'role'     => 'required|in_list[admin,user]',
        ];

        // Password optional check
        $password = $this->request->getVar('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'nama'     => $this->request->getVar('nama'),
            'username' => $this->request->getVar('username'),
            'role'     => $this->request->getVar('role'),
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        // Handle Photo Upload
        $file = $this->request->getFile('foto');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (strpos($file->getMimeType(), 'image/') === 0 && $file->getSize() <= 2097152) {
                 $newName = $file->getRandomName();
                 $file->move(ROOTPATH . 'public/uploads/profiles', $newName);
                 $data['foto'] = $newName;
            }
        }

        if ($this->model->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'User berhasil diupdate']);
        }

        return $this->failServerError('Gagal update user');
    }

    // DELETE /api/users/(:num)
    public function delete($id = null)
    {
        if (!$id) return $this->failNotFound('ID User tidak ditemukan');

        $user = $this->model->find($id);
        if (!$user) return $this->failNotFound('User tidak ditemukan');

        if ($user['is_master'] || $user['username'] === 'Rickylidya') { 
             return $this->failForbidden('Tidak bisa menghapus user master');
        }

        if ($this->model->delete($id)) {
            return $this->respondDeleted(['status' => 'success', 'message' => 'User berhasil dihapus']);
        }

        return $this->failServerError('Gagal menghapus user');
    }
}

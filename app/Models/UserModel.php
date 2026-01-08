<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'username', 'password', 'role', 'foto', 'status', 'is_master'];
    protected $useTimestamps = true;
    
    public function getUsersExceptMaster()
    {
        return $this->where('is_master', 0)->findAll();
    }
}
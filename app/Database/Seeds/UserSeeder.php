<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id' => 1,
            'nama' => 'Rickylidya',
            'username' => 'Rickylidya',
            'password' => password_hash('12345678', PASSWORD_DEFAULT),
            'role' => 'admin',
            'foto' => 'undraw_profile_2.svg',
            'is_master' => 1,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Ensure ID 1 is used
        // Check if exists
        $exists = $this->db->table('users')->where('id', 1)->countAllResults();
        
        if ($exists > 0) {
            $this->db->table('users')->where('id', 1)->update($data);
        } else {
            $this->db->table('users')->insert($data);
        }
    }
}

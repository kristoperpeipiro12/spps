<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Admin extends Seeder
{
    public function run()
    {
        $data = [
            'username'     => 'admin',
            'password'     => password_hash('12345', PASSWORD_DEFAULT),
            'nama_lengkap' => 'Kristoper',
            'email'        => 'halodek590@gmail.com',
        ];
        $this->db->table('admin')->insert($data);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyColumnAdmin extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('admin', ['token varchar(255)']);
    }

    public function down()
    {
        //
    }
}
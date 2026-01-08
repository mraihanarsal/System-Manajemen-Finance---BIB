<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToTiktokTransactions extends Migration
{
    public function up()
    {
        $fields = [
/*            'id_toko' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id'
            ], */
/*            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'profit'
            ], */
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'nama_barang'
            ],
            'deskripsi' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'kategori'
            ],
            'periode_start' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'deskripsi'
            ],
            'periode_end' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'periode_start'
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'periode_end'
            ]
        ];

        $this->forge->addColumn('tiktok_transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tiktok_transactions', ['id_toko', 'nama_barang', 'kategori', 'deskripsi', 'periode_start', 'periode_end', 'tanggal']);
    }
}

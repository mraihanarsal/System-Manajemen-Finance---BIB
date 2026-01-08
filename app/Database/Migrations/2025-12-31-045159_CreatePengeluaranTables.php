<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengeluaranTables extends Migration
{
    public function up()
    {
        // 1. Create Kategori Table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS kategori_pengeluaran (
              id INT AUTO_INCREMENT PRIMARY KEY,
              kode VARCHAR(50) UNIQUE NOT NULL,
              nama VARCHAR(100) NOT NULL,
              is_active TINYINT(1) DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");

        // 2. Insert Default Categories
        $this->db->query("
            INSERT IGNORE INTO kategori_pengeluaran (kode, nama) VALUES
            ('bahan_baku','Bahan Baku'),
            ('produksi','Biaya Produksi'),
            ('operasional','Operasional'),
            ('transport','Transportasi'),
            ('gaji','Gaji Karyawan'),
            ('pajak','Pajak'),
            ('marketing','Marketing'),
            ('affiliate','Affiliate'),
            ('lainnya','Lainnya');
        ");

        // 3. Create Pengeluaran Table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS pengeluaran (
              id BIGINT AUTO_INCREMENT PRIMARY KEY,
              periode DATE NOT NULL,
              kategori_id INT NOT NULL,
              deskripsi VARCHAR(255),
              jumlah DECIMAL(15,2) NOT NULL,
              created_by BIGINT NOT NULL,
              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
              CONSTRAINT chk_jumlah CHECK (jumlah > 0),
              INDEX idx_periode (periode),
              INDEX idx_kategori (kategori_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS pengeluaran");
        $this->db->query("DROP TABLE IF EXISTS kategori_pengeluaran");
    }
}

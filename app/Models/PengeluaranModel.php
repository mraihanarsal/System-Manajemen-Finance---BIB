<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table            = 'pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['periode', 'kategori_id', 'deskripsi', 'jumlah', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps    = true; 
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    // Fetch pengeluaran with category name
    public function getWithKategori($start = null, $end = null)
    {
        $builder = $this->select('pengeluaran.*, kategori_pengeluaran.nama as nama_kategori, kategori_pengeluaran.kode as kode_kategori')
                        ->join('kategori_pengeluaran', 'kategori_pengeluaran.id = pengeluaran.kategori_id', 'left');
        
        if ($start && $end) {
            $builder->where('periode >=', $start)
                    ->where('periode <=', $end);
        }

        return $builder->orderBy('periode', 'DESC')
                       ->orderBy('created_at', 'DESC')
                       ->findAll();
    }
}

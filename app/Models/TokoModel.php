<?php

namespace App\Models;

use CodeIgniter\Model;

class TokoModel extends Model
{
    protected $table = 'master_toko';
    protected $primaryKey = 'id_toko';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_toko',
        'nama_toko',
        'platform',
        'alamat',
        'created_by',
        'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // METHOD BARU: Filter toko by platform
    public function getTokoByPlatform($platform = 'shopee')
    {
        return $this->where('platform', $platform)
            ->findAll();
    }

    public function generateIdToko($platform = 'shopee')
    {
        $prefix = '';
        switch ($platform) {
            case 'tiktok':
                $prefix = 'TKTK';
                break;
            case 'zefatex':
                $prefix = 'ZFTX';
                break;
            default:
                $prefix = 'TK';
        }

        $lastToko = $this->where('platform', $platform)
            ->orderBy('id_toko', 'DESC')
            ->first();

        if ($lastToko) {
            $lastNumber = (int) substr($lastToko['id_toko'], strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function countTokoByPlatform($platform = 'shopee')
    {
        return $this->where('platform', $platform)
            ->countAllResults();
    }

    public function deactivate($id_toko)
    {
        return $this->update($id_toko, ['is_active' => 0]);
    }

    public function activate($id_toko)
    {
        return $this->update($id_toko, ['is_active' => 1]);
    }
}

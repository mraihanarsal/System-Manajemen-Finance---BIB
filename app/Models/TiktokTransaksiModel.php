<?php

namespace App\Models;

use CodeIgniter\Model;

class TiktokTransaksiModel extends Model
{
    protected $table            = 'tiktok_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'id_toko', // New
        'nama_barang', // New
        'kategori', // New
        'deskripsi', // New
        'periode_start', // New
        'periode_end', // New
        'tanggal', // New
        'order_id',
        'created_at',
        'settled_at',
        'currency',
        'settlement',
        'fees',
        'harga_modal',
        'profit',
        'created_input'
    ];

    // table ini tidak memakai updated_at / created_at CI
    protected $useTimestamps = false;

    // ===== FILTER RANGE (created_at) =====
    public function filterByDate($start, $end)
    {
        return $this->where('created_at >=', $start)
            ->where('created_at <=', $end)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    // ===== SUMMARY =====
    public function getSummary($id_toko, $start, $end)
    {
        
        $rows = $this->where('id_toko', $id_toko)
                     ->where('created_at >=', $start . ' 00:00:00')
                     ->where('created_at <=', $end . ' 23:59:59')
                     ->findAll();

        $summary = [
            'settlement' => array_sum(array_column($rows, 'settlement')),
            'fees'       => array_sum(array_column($rows, 'fees')),
            'harga_modal' => array_sum(array_column($rows, 'harga_modal')),
            'profit'     => array_sum(array_column($rows, 'profit')),
        ];

        return $summary;
    }
    // Ambil Riwayat Perhitungan Laba Grouping by Waktu Dibuat
    public function getRiwayatLaba($id_toko)
    {
        return $this->select('
                created_at,
                periode_start,
                periode_end,
                COUNT(id) as total_item,
                SUM(settlement) as total_revenue,
                SUM(profit) as total_laba
            ')
            ->where('id_toko', $id_toko)
            ->where('kategori !=', 'PENDAPATAN') // Filter agar tidak tercampur upload pendapatan
            ->groupBy(['created_at', 'periode_start', 'periode_end']) // Grouping berdasarkan timestamp simpan
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    // ===== GLOBAL REVENUE & PROFIT (ALL SHOPS) =====
    
    // Monthly History
    public function getGlobalPendapatanHistory()
    {
        return $this->select('YEAR(periode_start) as periode_tahun, MONTH(periode_start) as periode_bulan, SUM(settlement) as total_pendapatan, SUM(profit) as total_laba')
            ->groupBy('YEAR(periode_start), MONTH(periode_start)')
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC')
            ->findAll();
    }

    // Yearly History
    public function getGlobalPendapatanTahunan()
    {
        return $this->select('YEAR(periode_start) as periode_tahun, SUM(settlement) as total_pendapatan, SUM(profit) as total_laba')
            ->groupBy('YEAR(periode_start)')
            ->orderBy('periode_tahun', 'DESC')
            ->findAll();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'platform_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_toko',
        'platform',
        'nama_toko',
        'pendapatan_bersih',
        'periode_bulan',
        'periode_tahun',
        'tanggal_transaksi'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAllWithToko()
    {
        return $this->db->table('platform_transactions t')
            ->select('t.*, mt.alamat, mt.platform')
            ->join('master_toko mt', 't.id_toko = mt.id_toko')
            ->get()
            ->getResultArray();
    }

    public function getLaporanBulanan($tahun, $bulan)
    {
        return $this->db->table('platform_transactions')
            ->select('
                id_toko,
                nama_toko,
                platform, // TAMBAH INI
                COUNT(*) as total_transaksi,
                SUM(pendapatan_bersih) as pendapatan_bersih
            ')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->groupBy('id_toko, nama_toko, platform')
            ->get()
            ->getResultArray();
    }

    public function getTotalPendapatanBulanan($tahun, $bulan)
    {
        $result = $this->db->table('platform_transactions')
            ->select('
                SUM(pendapatan_bersih) as total_pendapatan_bersih,
                COUNT(*) as total_transaksi
            ')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->get()
            ->getRowArray();

        return $result ?: [
            'total_pendapatan_bersih' => 0,
            'total_transaksi' => 0
        ];
    }

    public function getTahunList()
    {
        $result = $this->db->table('platform_transactions')
            ->select('periode_tahun')
            ->distinct()
            ->orderBy('periode_tahun', 'DESC')
            ->get()
            ->getResultArray();

        $tahun_list = [];
        foreach ($result as $row) {
            $tahun_list[] = $row['periode_tahun'];
        }

        // Jika tidak ada data, default ke tahun sekarang
        if (empty($tahun_list)) {
            $tahun_list[] = date('Y');
        }

        return $tahun_list;
    }

    // METHOD BARU: Untuk filter by platform
    public function getByPlatform($platform = 'shopee')
    {
        return $this->where('platform', $platform)->findAll();
    }

    // METHOD BARU: Untuk dashboard shopee
    public function getShopeeLaporanBulanan($tahun, $bulan)
    {
        return $this->db->table('platform_transactions')
            ->select('
                id_toko,
                nama_toko,
                COUNT(*) as total_transaksi,
                SUM(pendapatan_bersih) as pendapatan_bersih
            ')
            ->where('platform', 'shopee')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->groupBy('id_toko, nama_toko')
            ->get()
            ->getResultArray();
    }

    public function getShopeeTotalPendapatanBulanan($tahun, $bulan)
    {
        $result = $this->db->table('platform_transactions')
            ->select('
                SUM(pendapatan_bersih) as total_pendapatan_bersih,
                COUNT(*) as total_transaksi
            ')
            ->where('platform', 'shopee')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->get()
            ->getRowArray();

        return $result ?: [
            'total_pendapatan_bersih' => 0,
            'total_transaksi' => 0
        ];
    }

    public function getTotalPendapatanByToko($id_toko, $tahun, $bulan)
    {
        $result = $this->where('id_toko', $id_toko)
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->selectSum('pendapatan_bersih')
            ->first();

        return $result ? $result->pendapatan_bersih : 0;
    }
    // ========== TIKTOK ==========
    public function getTiktokLaporanBulanan($tahun, $bulan)
    {
        return $this->db->table('platform_transactions')
            ->select('
            id_toko,
            nama_toko,
            COUNT(*) as total_transaksi,
            SUM(pendapatan_bersih) as pendapatan_bersih
        ')
            ->where('platform', 'tiktok')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->groupBy('id_toko, nama_toko')
            ->get()
            ->getResultArray();
    }

    public function getTiktokTotalPendapatanBulanan($tahun, $bulan)
    {
        $result = $this->db->table('platform_transactions')
            ->select('
            SUM(pendapatan_bersih) as total_pendapatan_bersih,
            COUNT(*) as total_transaksi
        ')
            ->where('platform', 'tiktok')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->get()
            ->getRowArray();

        return $result ?: [
            'total_pendapatan_bersih' => 0,
            'total_transaksi' => 0
        ];
    }
    // ========== ZEFATEX ==========
    public function getZefatexLaporanBulanan($tahun, $bulan)
    {
        return $this->db->table('platform_transactions')
            ->select('
            id_toko,
            nama_toko,
            COUNT(*) as total_transaksi,
            SUM(pendapatan_bersih) as pendapatan_bersih
        ')
            ->where('platform', 'zefatex')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->groupBy('id_toko, nama_toko')
            ->get()
            ->getResultArray();
    }

    public function getZefatexTotalPendapatanBulanan($tahun, $bulan)
    {
        $result = $this->db->table('platform_transactions')
            ->select('
            SUM(pendapatan_bersih) as total_pendapatan_bersih,
            COUNT(*) as total_transaksi
        ')
            ->where('platform', 'zefatex')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->get()
            ->getRowArray();

        return $result ?: [
            'total_pendapatan_bersih' => 0,
            'total_transaksi' => 0
        ];
    }
    // METHOD BARU: Untuk Shopee Global Revenue History (Semua Toko via Grouping Bulan/Tahun)
    public function getGlobalPendapatanHistory($platform = 'shopee')
    {
        if ($platform == 'shopee') {
            return $this->db->table('upload_reports')
                ->select('YEAR(periode_awal) as periode_tahun, MONTH(periode_awal) as periode_bulan, SUM(total_penghasilan) as total_pendapatan')
                ->groupBy('YEAR(periode_awal), MONTH(periode_awal)')
                ->orderBy('periode_tahun', 'DESC')
                ->orderBy('periode_bulan', 'DESC')
                ->get()
                ->getResultArray();
        }

        return $this->db->table($this->table)
            ->select('periode_tahun, periode_bulan, SUM(pendapatan_bersih) as total_pendapatan, COUNT(*) as total_transaksi')
            ->where('platform', $platform)
            ->groupBy('periode_tahun, periode_bulan')
            ->orderBy('periode_tahun', 'DESC')
            ->orderBy('periode_bulan', 'DESC')
            ->get()
            ->getResultArray();
    }

    // METHOD BARU: Global Revenue Annual Grouping
    public function getGlobalPendapatanTahunan($platform = 'shopee')
    {
        if ($platform == 'shopee') {
            return $this->db->table('upload_reports')
                ->select('YEAR(periode_awal) as periode_tahun, SUM(total_penghasilan) as total_pendapatan')
                ->groupBy('YEAR(periode_awal)')
                ->orderBy('periode_tahun', 'DESC')
                ->get()
                ->getResultArray();
        }

        return $this->db->table($this->table)
            ->select('periode_tahun, SUM(pendapatan_bersih) as total_pendapatan')
            ->where('platform', $platform)
            ->groupBy('periode_tahun')
            ->orderBy('periode_tahun', 'DESC')
            ->get()
            ->getResultArray();
    }
}

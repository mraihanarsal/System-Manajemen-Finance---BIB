<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\TokoModel;

class DetailTiktokController extends BaseController
{
    protected $transaksiModel;
    protected $tokoModel;

    public function __construct()
    {
        $this->transaksiModel = new \App\Models\TiktokTransaksiModel();
        $this->tokoModel = new TokoModel();
    }

    public function index($id_toko)
    {
        $toko = $this->tokoModel->find($id_toko);

        if (!$toko) {
            return redirect()->back()->with('error', 'Toko tidak ditemukan.');
        }

        // Ambil filter tanggal (default bulan ini)
        $start = $this->request->getGet('start') ?? date('Y-m-01');
        $end   = $this->request->getGet('end') ?? date('Y-m-t');

        // Ambil data transaksi (gabungan Pendapatan & Laba Items)
        // Kita mungkin ingin membedakan atau menampilkan semua.
        // Untuk detail toko, biasanya menampilkan list transaksi "Pendapatan" (Uploadan) 
        // DAN/ATAU list item "Laba" (Barang).
        
        // Strategi: Tampilkan List History Upload (Pendapatan) dan Summary Laba.
        
        // 1. Ambil Summary (Total Settlement, Profit) dalam range tanggal
        $summary = $this->transaksiModel->getSummary($id_toko, $start, $end); // Updated with ID Filter

        // 2. Ambil List Transaksi PENDAPATAN (Upload)
        $riwayatPendapatan = $this->transaksiModel
            ->where('id_toko', $id_toko)
            ->where('kategori', 'PENDAPATAN')
            ->where('created_at >=', $start . ' 00:00:00')
            ->where('created_at <=', $end . ' 23:59:59')
            ->orderBy('id', 'DESC')
            ->findAll();

        // 3. Ambil List Transaksi HASIL LABA (Barang)
        $riwayatLaba = $this->transaksiModel
            ->where('id_toko', $id_toko)
            ->where('kategori !=', 'PENDAPATAN')
            ->where('created_at >=', $start . ' 00:00:00')
            ->where('created_at <=', $end . ' 23:59:59')
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [
            'toko'              => $toko,
            'summary'           => $summary,
            'riwayatPendapatan' => $riwayatPendapatan,
            'riwayatLaba'       => $riwayatLaba,
            'start'             => $start,
            'end'               => $end
        ];

        return view('tiktok/detail/index', $data);
    }
}

<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\TokoModel;

class TransaksiController extends BaseController
{
    protected $transaksiModel;
    protected $tokoModel;
    protected $db;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->tokoModel = new TokoModel();
    }

    public function index()
    {
        $data['transaksi'] = $this->transaksiModel->getAllWithToko();
        return view('shopee/transaksi/index', $data);
    }

    public function create()
    {
        $data['toko'] = $this->tokoModel->findAll();
        return view('shopee/transaksi/modal-create', $data);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $this->transaksiModel->insert($data);
        return redirect()->to('/shopee/transaksi')->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function laporanBulanan()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bulan = $this->request->getGet('bulan') ?: date('m');

        $data['laporan'] = $this->transaksiModel->getLaporanBulanan($tahun, $bulan);
        return view('shopee/transaksi/laporan-bulanan', $data);
    }
    public function getTotalPendapatanBulanan($tahun, $bulan)
    {
        return $this->db->table('shoppe_transactions')
            ->select('
            SUM(subtotal) as total_penjualan,
            SUM(pendapatan_bersih) as total_pendapatan_bersih,
            SUM(ppn + pph) as total_pajak
        ')
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->get()
            ->getRowArray();
    }
}

<?php

namespace App\Controllers;

use App\Models\TokoModel;
use App\Models\TransaksiModel;

class TiktokController extends BaseController
{
    protected $tokoModel;
    protected $transaksiModel;

    public function __construct()
    {
        $this->tokoModel = new TokoModel();
        $this->transaksiModel = new TransaksiModel();
    }

    public function index()
    {
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bulan = $this->request->getGet('bulan') ?: date('m');


        $countModel1 = new TokoModel();
        $countModel2 = new TokoModel();

        $active_toko = $countModel1
            ->where('platform', 'tiktok')
            ->where('is_active', 1)
            ->countAllResults();

        $inactive_toko = $countModel2
            ->where('platform', 'tiktok')
            ->where('is_active', 0)
            ->countAllResults();

        $total_toko = (int) ($active_toko + $inactive_toko);


        $toko = $this->tokoModel
            ->where('platform', 'tiktok')
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate(5, 'default');

        $pager = $this->tokoModel->pager;

        $data = [
            'toko' => $toko,
            'pager' => $pager,
            'total_toko' => $total_toko,
            'active_toko' => $active_toko,
            'inactive_toko' => $inactive_toko,
            'transaksi' => $this->transaksiModel->getTiktokLaporanBulanan($tahun, $bulan),
            'total_pendapatan' => $this->transaksiModel->getTiktokTotalPendapatanBulanan($tahun, $bulan),
            'tahun' => $tahun,
            'bulan' => $bulan,
            'tahun_list' => $this->transaksiModel->getTahunList()
        ];

        return view('tiktok/index', $data);
    }

    public function pendapatan()
    {
        $ttModel = new \App\Models\TiktokTransaksiModel();

        $data = [
            'history_bulanan' => $ttModel->getGlobalPendapatanHistory(),
            'history_tahunan' => $ttModel->getGlobalPendapatanTahunan()
        ];

        return view('tiktok/pendapatan', $data);
    }
}

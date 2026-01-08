<?php

namespace App\Controllers;

use App\Models\TokoModel;
use App\Models\TransaksiModel;

class ShopeeController extends BaseController
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


        $toko = $this->tokoModel
            ->where('platform', 'shopee')
            ->where('is_active', 1)
            ->orderBy('id_toko', 'DESC')
            ->paginate(5, 'toko_shopee');


        $total_toko_aktif = $this->tokoModel
            ->where('platform', 'shopee')
            ->where('is_active', 1)
            ->countAllResults();

        $total_toko_nonaktif = $this->tokoModel
            ->where('platform', 'shopee')
            ->where('is_active', 0)
            ->countAllResults();

        $data = [
            'toko'                   => $toko,
            'pager'                  => $this->tokoModel->pager,

            'total_toko'             => $total_toko_aktif + $total_toko_nonaktif,
            'total_toko_aktif'       => $total_toko_aktif,
            'total_toko_nonaktif'    => $total_toko_nonaktif,

            'transaksi'              => $this->transaksiModel->getShopeeLaporanBulanan($tahun, $bulan),
            'total_pendapatan'       => $this->transaksiModel->getShopeeTotalPendapatanBulanan($tahun, $bulan),

            'tahun'                  => $tahun,
            'bulan'                  => $bulan,
            'tahun_list'             => $this->transaksiModel->getTahunList()
        ];

        return view('shopee/index', $data);
    }

    public function detail($id_toko)
    {
        $toko = $this->tokoModel->find($id_toko);

        if (!$toko) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Toko tidak ditemukan');
        }


        if ($toko['is_active'] == 0) {
            return redirect()->to('/shopee')->with('error', 'Toko ini sedang nonaktif');
        }

        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bulan = $this->request->getGet('bulan') ?: date('m');

        $data = [
            'id_toko'     => $id_toko,
            'nama_toko'   => $toko['nama_toko'] ?? 'Toko Shopee',
            'toko'        => $toko,

            'transaksi' => $this->transaksiModel
                ->where('id_toko', $id_toko)
                ->where('platform', $toko['platform'] ?? 'shopee')
                ->where('periode_tahun', $tahun)
                ->where('periode_bulan', $bulan)
                ->findAll(),

            'tahun'             => $tahun,
            'bulan'             => $bulan,
            'total_pendapatan'  => $this->transaksiModel->getTotalPendapatanByToko($id_toko, $tahun, $bulan)
        ];

        return view('shopee/detail/index', $data);
    }
    public function pendapatan()
    {
        $data = [
            'history_bulanan' => $this->transaksiModel->getGlobalPendapatanHistory('shopee'),
            'history_tahunan' => $this->transaksiModel->getGlobalPendapatanTahunan('shopee')
        ];

        return view('shopee/pendapatan', $data);
    }
}

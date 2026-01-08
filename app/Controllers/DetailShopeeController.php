<?php

namespace App\Controllers;

use App\Models\ReportModel;
use App\Models\TokoModel;

class DetailShopeeController extends BaseController
{
    protected $pdfModel;
    protected $tokoModel;

    public function __construct()
    {
        $this->pdfModel  = new ReportModel();
        $this->tokoModel = new TokoModel();
    }

    public function index($id_toko)
    {
        // FILTER RANGE TANGGAL
        $tanggal_dari   = $this->request->getGet('tanggal_dari');
        $tanggal_sampai = $this->request->getGet('tanggal_sampai');

        // Ambil nama toko
        $toko = $this->tokoModel->find($id_toko);
        $nama_toko = $toko['nama_toko'] ?? 'Toko Tidak Ditemukan';

        // BUILDER UTAMA untuk pagination + filter range
        $builder = $this->pdfModel->where('id_toko', $id_toko);

        if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
            // pastikan order tanggal benar (dari <= sampai)
            if (strtotime($tanggal_dari) > strtotime($tanggal_sampai)) {
                // swap
                $tmp = $tanggal_dari;
                $tanggal_dari = $tanggal_sampai;
                $tanggal_sampai = $tmp;
            }

            // include records whose periode_awal/periode_akhir overlap with range:
            // simplest: periode_awal >= tanggal_dari AND periode_akhir <= tanggal_sampai
            $builder->where('periode_awal >=', $tanggal_dari);
            $builder->where('periode_akhir <=', $tanggal_sampai);
        }

        // Pagination (5 per page)
        $reports = $builder->orderBy('periode_awal', 'DESC')->paginate(5);
        $pager   = $this->pdfModel->pager;

        // ===============================
        // TOTAL PENDAPATAN (GRAND total pada range / semua jika no range)
        // ===============================
        $totalBuilder = $this->pdfModel->selectSum('total_penghasilan')->where('id_toko', $id_toko);

        if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
            $totalBuilder->where('periode_awal >=', $tanggal_dari);
            $totalBuilder->where('periode_akhir <=', $tanggal_sampai);
        }

        $totalRow = $totalBuilder->first();
        // depending on CI version first() returns array
        $total_pendapatan = isset($totalRow['total_penghasilan']) ? (int)$totalRow['total_penghasilan'] : 0;

        // ===============================
        // JUMLAH LAPORAN + RATA-RATA BULANAN
        // ===============================
        $jumlah_laporan = is_array($reports) ? count($reports) : 0;
        $rata_rata_bulanan = ($jumlah_laporan > 0) ? ($total_pendapatan / $jumlah_laporan) : 0;

        // ===============================
        // TOTAL PER TAHUN (card list)
        // ===============================
        $yearQuery = $this->pdfModel
            ->select('YEAR(periode_awal) AS tahun, SUM(total_penghasilan) AS total')
            ->where('id_toko', $id_toko);

        if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
            $yearQuery->where('periode_awal >=', $tanggal_dari);
            $yearQuery->where('periode_akhir <=', $tanggal_sampai);
        }

        $per_tahun_raw = $yearQuery
            ->groupBy('YEAR(periode_awal)')
            ->orderBy('tahun', 'DESC')
            ->findAll();

        $per_tahun = [];
        foreach ($per_tahun_raw as $row) {
            $yr = $row['tahun'];
            $per_tahun[$yr] = (int)$row['total'];
        }

        // ===============================
        // TOTAL PER BULAN (card list)
        // - show last 12 months if no range, or months inside range if provided
        // ===============================
        $monthQuery = $this->pdfModel
            ->select("DATE_FORMAT(periode_awal, '%Y-%m') AS bulan, SUM(total_penghasilan) AS total")
            ->where('id_toko', $id_toko);

        if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
            $monthQuery->where('periode_awal >=', $tanggal_dari);
            $monthQuery->where('periode_akhir <=', $tanggal_sampai);
        } else {
            // default: last 12 months from today
            $d12 = date('Y-m-d', strtotime('-11 months', strtotime(date('Y-m-01'))));
            $monthQuery->where('periode_awal >=', $d12);
        }

        $per_bulan_raw = $monthQuery
            ->groupBy("DATE_FORMAT(periode_awal, '%Y-%m')")
            ->orderBy('bulan', 'DESC')
            ->findAll();

        $per_bulan = [];
        foreach ($per_bulan_raw as $row) {
            $per_bulan[$row['bulan']] = (int)$row['total'];
        }

        // ===============================
        // PERFORMA - hitung stabilitas berdasarkan total per bulan (koefisien variasi sederhana)
        // ===============================
        // Ambil array monthly totals untuk analisis (ascending by bulan)
        $analysisQuery = $this->pdfModel
            ->select("DATE_FORMAT(periode_awal, '%Y-%m') AS bulan, SUM(total_penghasilan) AS total")
            ->where('id_toko', $id_toko);

        if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
            $analysisQuery->where('periode_awal >=', $tanggal_dari);
            $analysisQuery->where('periode_akhir <=', $tanggal_sampai);
        } else {
            // semua data
        }

        $analysis_raw = $analysisQuery
            ->groupBy("DATE_FORMAT(periode_awal, '%Y-%m')")
            ->orderBy('bulan', 'ASC')
            ->findAll();

        $monthly_totals = [];
        foreach ($analysis_raw as $row) {
            $monthly_totals[] = (float)$row['total'];
        }

        $performance_status = 'Tidak Cukup Data';
        $performance_score = 0.0;

        if (count($monthly_totals) >= 2) {
            // mean
            $n = count($monthly_totals);
            $mean = array_sum($monthly_totals) / $n;

            // variance (population)
            $variance = 0.0;
            foreach ($monthly_totals as $val) {
                $variance += pow(($val - $mean), 2);
            }
            $variance = $variance / $n;
            $stddev = sqrt($variance);

            // coefficient of variation (stddev / mean)
            $cv = $mean > 0 ? ($stddev / $mean) : 0;
            $performance_score = $cv;

            // thresholds (adjustable)
            if ($cv <= 0.20) {
                $performance_status = 'STABIL';
            } elseif ($cv <= 0.50) {
                $performance_status = 'KURANG STABIL';
            } else {
                $performance_status = 'TIDAK STABIL';
            }
        } elseif (count($monthly_totals) === 1) {
            $performance_status = 'Data Bulanan';
            $performance_score = 0.0;
        }

        // Periode Aktif (informasi di UI)
        if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
            $periode_aktif = date('d M Y', strtotime($tanggal_dari)) . ' - ' . date('d M Y', strtotime($tanggal_sampai));
        } else {
            $periode_aktif = 'Semua Periode';
        }

        // Data array untuk view
        $data = [
            'id_toko'            => $id_toko,
            'nama_toko'          => $nama_toko,
            'reports'            => $reports,
            'pager'              => $pager,

            'tanggal_dari'       => $tanggal_dari,
            'tanggal_sampai'     => $tanggal_sampai,

            'total_pendapatan'   => $total_pendapatan,
            'rata_rata_bulanan'  => $rata_rata_bulanan,
            'jumlah_laporan'     => $jumlah_laporan,
            'periode_aktif'      => $periode_aktif,

            'per_tahun'          => $per_tahun,
            'per_bulan'          => $per_bulan,

            'performance_status' => $performance_status,
            'performance_score'  => round($performance_score * 100, 2),
        ];

        return view('shopee/detail/index', $data);
    }
}

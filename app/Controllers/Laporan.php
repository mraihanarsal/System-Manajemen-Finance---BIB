<?php

namespace App\Controllers;

use App\Models\ReportModel; // Shopee (Upload Reports)
use App\Models\TiktokTransaksiModel; // Tiktok
use App\Models\ZefatexModel; // Zefatex
use App\Models\PengeluaranModel; // Pengeluaran
use CodeIgniter\API\ResponseTrait;

class Laporan extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $data = [
            'title' => 'Laporan Keuangan',
        ];

        return view('laporan/index', $data);
    }

    public function getData()
    {
        try {
            $result = $this->getReportData();
            return $this->response->setJSON(['data' => $result]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function generate_pdf()
    {
        // Check if Dompdf is installed
        if (!class_exists('Dompdf\Dompdf')) {
            throw new \Exception('Dompdf library not found. Please run "composer require dompdf/dompdf"');
        }

        $dataReport = $this->getReportData();
        
        $data = [
            'report_data' => $dataReport,
            'title' => 'Laporan Keuangan',
            'generated_at' => date('d F Y H:i'),
            'filter_info' => 'Semua Periode'
        ];

        $html = view('laporan/laporan_pdf', $data);

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream("Laporan_Keuangan_".date('YmdHis').".pdf", ["Attachment" => false]);
    }

    private function getReportData()
    {
        $shopeeModel = new ReportModel(); 
        $tiktokModel = new TiktokTransaksiModel();
        $zefatexModel = new ZefatexModel();
        $pengeluaranModel = new PengeluaranModel();

        // 1. Get Monthly Data
        
        // Shopee (Revenue)
        $shopeeData = $shopeeModel->select("DATE_FORMAT(periode_awal, '%Y-%m') as periode, SUM(total_penghasilan) as total")
            ->groupBy("DATE_FORMAT(periode_awal, '%Y-%m')")
            ->findAll();

        // Tiktok (Profit)
        $tiktokData = $tiktokModel->select("DATE_FORMAT(periode_start, '%Y-%m') as periode, SUM(profit) as total")
            ->where('kategori !=', 'PENDAPATAN')
            ->groupBy("DATE_FORMAT(periode_start, '%Y-%m')")
            ->findAll();

        // Zefatex (Revenue)
        $zefatexData = $zefatexModel->select("DATE_FORMAT(transaction_date, '%Y-%m') as periode, SUM(total_amount) as total")
            ->groupBy("DATE_FORMAT(transaction_date, '%Y-%m')")
            ->findAll();

        // Pengeluaran
        $expensesData = $pengeluaranModel->select("DATE_FORMAT(periode, '%Y-%m') as periode, SUM(jumlah) as total")
            ->groupBy("DATE_FORMAT(periode, '%Y-%m')")
            ->findAll();

        // 2. Aggregate by Period
        $aggregated = [];

        // Helper to merge
        $merge = function($data, $type) use (&$aggregated) {
            foreach ($data as $row) {
                $p = $row['periode']; // YYYY-MM
                if (!$p) continue;
                if (!isset($aggregated[$p])) {
                    $aggregated[$p] = [
                        'periode' => $p, 
                        'pemasukan' => 0, 
                        'pengeluaran' => 0
                    ];
                }
                if ($type === 'income') {
                    $aggregated[$p]['pemasukan'] += (float)$row['total'];
                } elseif ($type === 'expense') {
                    $aggregated[$p]['pengeluaran'] += (float)$row['total'];
                }
            }
        };

        $merge($shopeeData, 'income');
        $merge($tiktokData, 'income');
        $merge($zefatexData, 'income');
        $merge($expensesData, 'expense');

        // 3. Format Result
        $result = [];
        foreach ($aggregated as $p => $val) {
            $date = \DateTime::createFromFormat('Y-m', $p);
            $cleanProfit = $val['pemasukan'] - $val['pengeluaran'];
            
            $result[] = [
                'bulan' => $date->format('m'), // 01-12
                'tahun' => $date->format('Y'),
                'nama_bulan' => $this->bulanIndo($date->format('n')),
                'pemasukan' => $val['pemasukan'],
                'pengeluaran' => $val['pengeluaran'],
                'bersih' => $cleanProfit,
                'periode_sort' => $p
            ];
        }

        // Sort Descending by Period
        usort($result, function($a, $b) {
            return strcmp($b['periode_sort'], $a['periode_sort']);
        });

        return $result;
    }

    private function bulanIndo($num)
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[(int)$num] ?? '';
    }
}

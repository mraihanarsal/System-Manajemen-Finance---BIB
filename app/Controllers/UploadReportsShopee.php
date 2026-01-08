<?php

namespace App\Controllers;

use App\Models\ReportModel;
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Config;

class UploadReportsShopee extends BaseController
{
    public function index($id_toko)
    {
        $model = new ReportModel();

        $tokoModel = new \App\Models\TokoModel();
        $toko = $tokoModel->find($id_toko);

        $data = [
            'id_toko'  => $id_toko,
            'nama_toko'  => $toko['nama_toko'] ?? 'Toko tidak ditemukan',
            'reports'  => $model->where('id_toko', $id_toko)
                ->orderBy('id', 'DESC')
                ->paginate(5),
            'pager'    => $model->pager
        ];

        return view('shopee/transaksi/index', $data);
    }

    public function upload($id_toko)
    {
        $file = $this->request->getFile('pdf_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        $model = new ReportModel();

        // Simpan file random name
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/transaksi', $newName);

        $filePath = WRITEPATH . 'uploads/transaksi/' . $newName;

        // Parse PDF
        $config = new Config();
        $config->setIgnoreEncryption(true);

        $parser = new Parser([], $config);
        $pdf    = $parser->parseFile($filePath);
        $text   = $pdf->getText();


        if (stripos($text, 'Terima kasih telah menggunakan Shopee.') === false) {
            unlink($filePath);
            return redirect()->back()->with(
                'error',
                'Upload gagal! Pastikan file yang di-upload adalah PDF resmi yang di-download dari Shopee (fitur penjual).'
            );
        }

        // Ambil report code (ID unik Shopee)
        $reportCode = null;
        if (preg_match('/\b(\d{14,15})\b/', $text, $m)) {
            $reportCode = trim($m[1]);
        }

        // Cek duplikasi berdasarkan report code
        if ($model->where(['id_toko' => $id_toko, 'report_code' => $reportCode])->first()) {
            return redirect()->back()->with('error', 'Laporan ini sudah pernah diupload sebelumnya!');
        }

        // Ambil username
        $username = null;
        if (preg_match('/Username\s*:\s*([A-Za-z0-9._]+)/i', $text, $m)) {
            $username = trim($m[1]);
        }

        // Periode
        $periode_awal = null;
        $periode_akhir = null;
        if (preg_match('/(\d{4}-\d{2}-\d{2})\s*sampai\s*(\d{4}-\d{2}-\d{2})/i', $text, $m)) {
            $periode_awal  = $m[1];
            $periode_akhir = $m[2];
        }

        // Total Penghasilan
        $total_penghasilan = 0;
        if (preg_match('/Total\s*Penghasilan\s*Rp?\s*([0-9.,]+)/i', $text, $m)) {
            $angka = str_replace(['.', ','], '', $m[1]);
            $total_penghasilan = (int) $angka;
        }

        // Simpan ke database
        $model->insert([
            'id_toko'           => $id_toko,
            'report_code'       => $reportCode,
            'username'          => $username,
            'nama_file'         => $newName,
            'periode_awal'      => $periode_awal,
            'periode_akhir'     => $periode_akhir,
            'total_penghasilan' => $total_penghasilan,
            'tanggal_upload'    => date('Y-m-d')
        ]);

        return redirect()->back()->with('success', 'PDF berhasil diupload & diproses');
    }

    public function detail($id_toko)
    {
        $model = new ReportModel();

        // Get filter parameters
        $tahun = $this->request->getGet('tahun') ?: date('Y');
        $bulan = $this->request->getGet('bulan') ?: '';

        // Build query
        $builder = $model->where('id_toko', $id_toko);

        if (!empty($tahun)) {
            $builder->where('YEAR(periode_awal)', $tahun);
        }

        if (!empty($bulan)) {
            $builder->where('MONTH(periode_awal)', $bulan);
        }

        // Get data with pagination
        $reports = $builder->orderBy('periode_awal', 'DESC')
            ->paginate(10);
        $pager = $model->pager;

        // Get statistics
        $total_pendapatan = $builder->selectSum('total_penghasilan')->get()->getRow()->total_penghasilan ?: 0;
        $jumlah_laporan = $builder->countAllResults();

        // Calculate average monthly income
        $rata_rata_bulanan = $jumlah_laporan > 0 ? $total_pendapatan / $jumlah_laporan : 0;

        // Get year list for filter
        $tahun_list = $model->distinct()
            ->select('YEAR(periode_awal) as tahun')
            ->where('id_toko', $id_toko)
            ->orderBy('tahun', 'DESC')
            ->findAll();

        $tahun_list = array_column($tahun_list, 'tahun');

        // Month list
        $bulan_list = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        // Chart data
        $chart_data = $this->getChartData($id_toko, $tahun, $bulan);

        $data = [
            'id_toko' => $id_toko,
            'reports' => $reports,
            'pager' => $pager,
            'total_pendapatan' => $total_pendapatan,
            'rata_rata_bulanan' => $rata_rata_bulanan,
            'jumlah_laporan' => $jumlah_laporan,
            'periode_aktif' => !empty($bulan) ? $bulan_list[$bulan] . ' ' . $tahun : 'Semua Periode',
            'selected_tahun' => $tahun,
            'selected_bulan' => $bulan,
            'tahun_list' => $tahun_list,
            'bulan_list' => $bulan_list,
            'chart_title' => !empty($tahun) ? 'Tahun ' . $tahun : 'Semua Tahun',
            'chart_labels' => $chart_data['labels'],
            'chart_data' => $chart_data['data']
        ];

        return view('shopee/detail/index', $data);
    }

    private function getChartData($id_toko, $tahun = '', $bulan = '')
    {
        $model = new ReportModel();
        $builder = $model->where('id_toko', $id_toko);

        $default_labels = [];
        $default_data = [];

        if (empty($tahun)) {

            $result = $builder->select('YEAR(periode_awal) as tahun, SUM(total_penghasilan) as total')
                ->groupBy('YEAR(periode_awal)')
                ->orderBy('tahun', 'ASC')
                ->findAll();

            $labels = array_column($result, 'tahun');
            $data = array_column($result, 'total');
        } else if (empty($bulan)) {
            // Monthly chart for selected year - group by month
            $result = $builder->select('MONTH(periode_awal) as bulan, SUM(total_penghasilan) as total')
                ->where('YEAR(periode_awal)', $tahun)
                ->groupBy('MONTH(periode_awal)')
                ->orderBy('bulan', 'ASC')
                ->findAll();

            $bulan_names = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $labels = $bulan_names;
            $data = array_fill(0, 12, 0);

            foreach ($result as $row) {
                $data[$row['bulan'] - 1] = $row['total'];
            }

            $labels = $bulan_names;
        } else {
            // Daily chart for selected month
            $result = $builder->select('periode_awal, total_penghasilan')
                ->where('YEAR(periode_awal)', $tahun)
                ->where('MONTH(periode_awal)', $bulan)
                ->orderBy('periode_awal', 'ASC')
                ->findAll();

            $labels = [];
            $data = [];

            foreach ($result as $row) {
                $labels[] = date('d M', strtotime($row['periode_awal']));
                $data[] = $row['total_penghasilan'];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function delete($id)
    {
        $model = new ReportModel();
        $data = $model->find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }


        $path = WRITEPATH . 'uploads/transaksi/' . $data['nama_file'];
        if (file_exists($path)) {
            unlink($path);
        }


        $model->delete($id);

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}

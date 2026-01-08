<?php

namespace App\Controllers;

use App\Models\TokoModel;
use App\Models\TiktokTransaksiModel;
use CodeIgniter\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TiktokTransaksiController extends Controller
{
    protected $tokoModel;
    protected $transaksiModel;

    public function __construct()
    {
        $this->tokoModel      = new TokoModel();
        $this->transaksiModel = new TiktokTransaksiModel();
    }

    /* ===========================================================
     *  HALAMAN PENDAPATAN
     * ===========================================================*/
    public function pendapatan($id_toko)
    {
        $toko = $this->tokoModel->find($id_toko);

        if (!$toko) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Toko tidak ditemukan');
        }

        // Default tanggal
        $start = $this->request->getGet('start') ?? date('Y-m-01');
        $end = $this->request->getGet('end') ?? date('Y-m-t');

        // Ambil histori pendapatan yang sudah diupload (untuk ditampilkan di tabel bawah form)
        // Filter kategori 'PENDAPATAN' agar tidak campur dengan data hasil hitung laba
        $history = $this->transaksiModel
            ->where('id_toko', $id_toko)
            ->where('kategori', 'PENDAPATAN')
            ->orderBy('id', 'DESC')
            ->limit(10) // Tampilkan 10 terakhir
            ->findAll();

        return view('tiktok/transaksi/pendapatan', [
            'id_toko'       => $id_toko,
            'toko'          => $toko,
            'start'         => $start,
            'end'           => $end,
            'preview_info'  => session()->getFlashdata('preview_info'), // Info sukses upload barusan
            'history'       => $history
        ]);
    }

    /* ===========================================================
     *  HALAMAN LABA
     * ===========================================================*/
    public function laba($id_toko)
    {
        $toko = $this->tokoModel->find($id_toko);

        if (!$toko) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Toko tidak ditemukan');
        }

        // Default tanggal
        $start = $this->request->getGet('start') ?? date('Y-m-01');
        $end = $this->request->getGet('end') ?? date('Y-m-t');

        // Ambil barang dari session
        $items = session()->get("tiktok_items_$id_toko") ?? [];

        // Ambil total settlement dari session (setelah pendapatan disimpan)
        $totalSettlement = session()->get("total_settlement_$id_toko") ?? 0;
        $periodeSettlement = session()->get("periode_settlement_$id_toko") ?? "$start s/d $end";

        // Jika ada preview dari proses
        $preview = session()->getFlashdata('preview_laba');
        $totalLaba = session()->getFlashdata('total_laba');
        $periode = session()->getFlashdata('periode_laba');

        // Ambil Riwayat Laba
        $historyLaba = $this->transaksiModel->getRiwayatLaba($id_toko);

        return view('tiktok/transaksi/laba', [
            'id_toko'            => $id_toko,
            'toko'               => $toko,
            'start'              => $start,
            'end'                => $end,
            'items'              => $items,
            'preview'            => $preview,
            'totalLaba'          => $totalLaba,
            'periode'            => $periode,
            'totalSettlement'    => $totalSettlement,
            'periodeSettlement'  => $periodeSettlement,
            'historyLaba'        => $historyLaba // Data baru
        ]);
    }

    /* ===========================================================
     *  RESET DATA LABA
     * ===========================================================*/
    public function resetLaba($id_toko)
    {
        session()->remove("pending_laba_$id_toko");
        session()->remove("tiktok_items_$id_toko");
        return redirect()->to("/tiktok/transaksi/laba/$id_toko")
            ->with('success', 'Data laba telah direset.');
    }

    /* ===========================================================
     *  RESET DATA PENDAPATAN
     * ===========================================================*/
    public function resetPendapatan($id_toko)
    {
        session()->remove("pending_pendapatan_$id_toko");
        session()->remove("total_settlement_$id_toko");
        session()->remove("periode_settlement_$id_toko");
        return redirect()->to("/tiktok/transaksi/pendapatan/$id_toko")
            ->with('success', 'Data pendapatan telah direset.');
    }

    /* ===========================================================
     *  PROSES UPLOAD PENDAPATAN - VERSI OPTIMIZED
     * ===========================================================*/
    public function processPendapatan($id_toko)
    {
        $file = $this->request->getFile('file');
        $userStart = $this->request->getPost('start');
        $userEnd = $this->request->getPost('end');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        $ext = strtolower($file->getClientExtension());

        // Validasi ekstensi file
        $allowedExt = ['csv', 'xlsx', 'xls'];
        if (!in_array($ext, $allowedExt)) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan CSV, XLS, atau XLSX.');
        }

        // Upload sementara
        $uploadPath = WRITEPATH . 'uploads/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

        $fname = time() . '_' . $file->getRandomName();
        $file->move($uploadPath, $fname);
        $fpath = $uploadPath . $fname;

        try {
            // Baca data dari file TikTok
            $tiktokData = $this->extractTikTokDataSimple($fpath, $ext);

            if ($tiktokData['total_settlement'] <= 0) {
                throw new \Exception("Total settlement tidak valid atau bernilai 0. Pastikan file adalah laporan TikTok (sheet 'Reports').");
            }

            // Gunakan periode dari file jika tersedia, jika tidak gunakan dari input user
            $start = !empty($tiktokData['periode_start']) ? $tiktokData['periode_start'] : $userStart;
            $end = !empty($tiktokData['periode_end']) ? $tiktokData['periode_end'] : $userEnd;

            $totalSettlement = $tiktokData['total_settlement'];

            // PREVIEW
            $previewData = [
                [
                    'periode'    => "$start s/d $end",
                    'settlement' => $totalSettlement,
                    'periode_file' => !empty($tiktokData['periode_start']) ?
                        date('d/m/Y', strtotime($tiktokData['periode_start'])) . ' - ' .
                        date('d/m/Y', strtotime($tiktokData['periode_end'])) :
                        'Input User'
                ]
            ];

            // DATA YANG AKAN DISIMPAN
            $dbData = [
                'id_toko'       => $id_toko,
                'order_id'      => null,
                'tanggal'       => null,
                'settlement'    => $totalSettlement,
                'fees'          => 0,
                'harga_modal'   => 0,
                'profit'        => $totalSettlement,
                'nama_barang'   => 'TOTAL TOKO',
                'kategori'      => 'PENDAPATAN',
                'deskripsi'     => 'Pendapatan TikTok per periode',
                'periode_start' => $start,
                'periode_end'   => $end,
                'created_at'    => date('Y-m-d H:i:s')
            ];

            // Simpan ke database dengan error handling
            if (!$this->transaksiModel->insert($dbData)) {
                $errors = $this->transaksiModel->errors();
                $errorMsg = 'Gagal menyimpan data ke database. ';
                if ($errors) {
                    $errorMsg .= implode(', ', $errors);
                }
                throw new \Exception($errorMsg);
            }
            
            $newId = $this->transaksiModel->getInsertID();

            // Simpan settlement untuk halaman laba (sesi tetap dibutuhkan untuk laba)
            session()->set("total_settlement_$id_toko", $totalSettlement);
            session()->set("periode_settlement_$id_toko", "$start s/d $end");
            session()->set("pending_pendapatan_id_$id_toko", $newId); // Simpan ID untuk dihapus saat saveLaba

        } catch (\Throwable $e) {
            @unlink($fpath);
            log_message('error', '[TikTokUpload] Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        @unlink($fpath);

        return redirect()->to("/tiktok/transaksi/pendapatan/$id_toko?start=" . urlencode($start) . "&end=" . urlencode($end))
            ->with('preview_pendapatan', $previewData)
            ->with('total_pendapatan_bersih', $totalSettlement)
            ->with('periode_pendapatan', "$start s/d $end")
            ->with('success', "Upload berhasil. Data tersimpan dengan ID: $newId.");
    }

    /* ===========================================================
     *  SIMPAN PENDAPATAN KE DATABASE
     * ===========================================================*/
    public function savePendapatan($id_toko)
    {
        $data = session()->get("pending_pendapatan_$id_toko");

        if (!$data) {
            return redirect()->to("/tiktok/transaksi/pendapatan/$id_toko")
                ->with('error', 'Tidak ada data yang siap disimpan.');
        }

        // Ambil data settlement untuk laba
        $totalSettlement = $data[0]['settlement'] ?? 0;
        $periodeStart = $data[0]['periode_start'] ?? '';
        $periodeEnd = $data[0]['periode_end'] ?? '';

        // Simpan ke database
        $this->transaksiModel->insertBatch($data);

        // Simpan settlement untuk halaman laba
        session()->set("total_settlement_$id_toko", $totalSettlement);
        session()->set("periode_settlement_$id_toko", "$periodeStart s/d $periodeEnd");

        // Hapus session data pendapatan
        session()->remove("pending_pendapatan_$id_toko");

        return redirect()->to("/tiktok/transaksi/laba/$id_toko")
            ->with('success', 'Pendapatan berhasil disimpan. Sekarang tambahkan barang untuk menghitung laba.')
            ->with('settlement_info', [
                'total' => $totalSettlement,
                'periode' => "$periodeStart s/d $periodeEnd"
            ]);
    }

    /* ===========================================================
     *  PROSES HITUNG LABA
     * ===========================================================*/
    public function processLaba($id_toko)
    {
        // Ambil total settlement dari session
        $totalSettlement = session()->get("total_settlement_$id_toko") ?? 0;

        if ($totalSettlement <= 0) {
            return redirect()->back()->with('error', 'Silakan simpan pendapatan terlebih dahulu.');
        }

        // Ambil periode dari session
        $periodeSettlement = session()->get("periode_settlement_$id_toko") ?? '';
        [$start, $end] = explode(' s/d ', $periodeSettlement);

        // Ambil barang dari session
        $items = session()->get("tiktok_items_$id_toko") ?? [];

        if (empty($items)) {
            return redirect()->back()->with('error', 'Silakan tambahkan barang terlebih dahulu.');
        }

        // Validasi bobot
        $totalBobot = array_sum(array_column($items, 'bobot'));
        if (round($totalBobot, 2) !== 100.0) {
            return redirect()->back()
                ->with('error', 'Total bobot barang harus 100%. Saat ini: ' . $totalBobot . '%');
        }

        // HITUNG LABA ALOKASI PER BARANG
        $previewData = [];
        $sessionData = [];
        $totalLaba   = 0;

        foreach ($items as $item) {
            $bobot         = floatval($item['bobot'] ?? 0);
            $modalPeriode  = floatval($item['modal_periode'] ?? 0);
            $settlementItem = $totalSettlement * ($bobot / 100);
            $labaItem       = $settlementItem - $modalPeriode;

            $previewData[] = [
                'nama'               => $item['nama_barang'],
                'bobot'              => $bobot,
                'settlement_barang'  => $settlementItem,
                'modal'              => $modalPeriode,
                'laba'               => $labaItem,
            ];

            $sessionData[] = [
                'id_toko'        => $id_toko,
                'order_id'       => null,
                'tanggal'        => null,
                'settlement'     => $settlementItem,
                'fees'           => 0,
                'harga_modal'    => $modalPeriode,
                'profit'         => $labaItem,
                'nama_barang'    => $item['nama_barang'],
                'kategori'       => $item['kategori'],
                'deskripsi'      => $item['deskripsi'],
                'periode_start'  => $start,
                'periode_end'    => $end,
                'created_at'     => date('Y-m-d H:i:s'),
            ];

            $totalLaba += $labaItem;
        }

        // Simpan ke session untuk tombol "Simpan"
        session()->set("pending_laba_$id_toko", $sessionData);

        return redirect()->to("/tiktok/transaksi/laba/$id_toko")
            ->with('preview_laba', $previewData)
            ->with('total_laba', $totalLaba)
            ->with('periode_laba', $periodeSettlement)
            ->with('success', 'Laba berhasil dihitung menggunakan metode alokasi.');
    }

    /* ===========================================================
     *  SIMPAN LABA KE DATABASE
     * ===========================================================*/
    public function saveLaba($id_toko)
    {
        $data = session()->get("pending_laba_$id_toko");

        if (!$data) {
            return redirect()->to("/tiktok/transaksi/laba/$id_toko")
                ->with('error', 'Tidak ada data yang siap disimpan.');
        }

        // Simpan ke database
        $this->transaksiModel->insertBatch($data);

        // Hapus data "PENDAPATAN" awal agar tidak double counting di dashboard
        $pendingId = session()->get("pending_pendapatan_id_$id_toko");
        if ($pendingId) {
            $this->transaksiModel->delete($pendingId);
            session()->remove("pending_pendapatan_id_$id_toko");
        }

        // Hapus session data
        session()->remove("pending_laba_$id_toko");
        session()->remove("total_settlement_$id_toko");
        session()->remove("periode_settlement_$id_toko");
        session()->remove("tiktok_items_$id_toko");

        return redirect()->to("/tiktok/detail/$id_toko")
            ->with('success', 'Data laba berhasil disimpan.');
    }

    // DELETE RIWAYAT LABA
    public function deleteLabaHistory($id_toko)
    {
        $createdAt = $this->request->getGet('date');

        if(!$createdAt) {
             return redirect()->back()->with('error', 'Tanggal tidak valid.');
        }

        // Hapus data berdasarkan created_at dan id_toko
        // Karena created_at string 'YYYY-MM-DD HH:MM:SS', kita harus match exact
        $this->transaksiModel
             ->where('id_toko', $id_toko)
             ->where('created_at', $createdAt)
             ->where('kategori !=', 'PENDAPATAN')
             ->delete();

        return redirect()->back()->with('success', 'Riwayat perhitungan laba berhasil dihapus.');
    }

    // DELETE SINGLE TRANSACTION (Untuk Halaman Detail)
    public function deleteTransaction($id)
    {
        // Cek apakah data ada
        $transaksi = $this->transaksiModel->find($id);
        if (!$transaksi) {
            return redirect()->back()->with('error', 'Data transaksi tidak ditemukan.');
        }

        $this->transaksiModel->delete($id);

        return redirect()->back()->with('success', 'Data transaksi berhasil dihapus.');
    }

    // DELETE ALL PENDAPATAN
    public function deleteAllPendapatan($id_toko)
    {
        $this->transaksiModel
             ->where('id_toko', $id_toko)
             ->where('kategori', 'PENDAPATAN')
             ->delete();

        return redirect()->back()->with('success', 'Semua riwayat pendapatan berhasil dihapus.');
    }

    // DELETE ALL LABA
    public function deleteAllLaba($id_toko)
    {
        $this->transaksiModel
             ->where('id_toko', $id_toko)
             ->where('kategori !=', 'PENDAPATAN')
             ->delete();

        return redirect()->back()->with('success', 'Semua riwayat perhitungan laba berhasil dihapus.');
    }

    /* ===========================================================
     *  ITEM BARANG MANAGEMENT
     * ===========================================================*/
    public function addBarang($id_toko)
    {
        $nama = $this->request->getPost('nama_barang');
        $kategori = $this->request->getPost('kategori');
        $modal = floatval($this->request->getPost('harga_modal') ?? 0);
        $deskripsi = $this->request->getPost('deskripsi');
        $bobot = floatval($this->request->getPost('bobot') ?? 0);
        $modal_periode = floatval($this->request->getPost('modal_periode') ?? 0);

        if (!$nama || !$modal) {
            return redirect()->back()->with('error', 'Nama & modal wajib diisi.');
        }

        $items = session()->get("tiktok_items_$id_toko") ?? [];

        // VALIDASI BOBOT TOTAL TIDAK BOLEH LEBIH DARI 100%
        $currentTotal = array_sum(array_column($items, 'bobot'));
        if (($currentTotal + $bobot) > 100) {
            return redirect()->back()->with('error', 'Gagal: Total bobot akan melebihi 100% (Saat ini: ' . $currentTotal . '%, Ditambah: ' . $bobot . '% = ' . ($currentTotal + $bobot) . '%). Kurangi bobot barang ini.');
        }

        $items[] = [
            'id' => uniqid(),
            'nama_barang' => $nama,
            'kategori' => $kategori,
            'harga_modal' => $modal,
            'deskripsi' => $deskripsi,
            'bobot' => $bobot,
            'modal_periode' => $modal_periode
        ];

        session()->set("tiktok_items_$id_toko", $items);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function deleteBarang($id_toko, $itemId)
    {
        $items = session()->get("tiktok_items_$id_toko") ?? [];

        $items = array_filter($items, fn($i) => $i['id'] !== $itemId);

        session()->set("tiktok_items_$id_toko", $items);

        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }

    public function clearBarang($id_toko)
    {
        session()->remove("tiktok_items_$id_toko");
        return redirect()->back()->with('success', 'Semua barang dihapus.');
    }

    /* ===========================================================
     *  REPORTING
     * ===========================================================*/
    public function report($id_toko)
    {
        $start = $this->request->getGet('start') ?? date('Y-m-01');
        $end   = $this->request->getGet('end') ?? date('Y-m-t');

        $rows = $this->transaksiModel
            ->where('id_toko', $id_toko)
            ->where('created_at >=', $start)
            ->where('created_at <=', $end)
            ->findAll();

        $summary = [
            'settlement' => array_sum(array_column($rows, 'settlement')),
            'fees'       => array_sum(array_column($rows, 'fees')),
            'harga_modal' => array_sum(array_column($rows, 'harga_modal')),
            'profit'     => array_sum(array_column($rows, 'profit')),
        ];

        return view('tiktok/transaksi/report', [
            'id_toko' => $id_toko,
            'toko'    => $this->tokoModel->find($id_toko),
            'start'   => $start,
            'end'     => $end,
            'rows'    => $rows,
            'summary' => $summary
        ]);
    }

    /* ===========================================================
     *  EKSTRAK DATA TIKTOK DARI SHEET "REPORTS"
     * ===========================================================*/
    private function extractTikTokDataSimple(string $path, string $ext): array
    {
        if ($ext === 'csv') {
            return $this->readTikTokCSV($path);
        }

        // Excel → langsung strict
        return $this->readTikTokExcel($path, $ext);
    }

    /* ===========================================================
     *  BACA EXCEL TIKTOK DENGAN PHPSPREADSHEET
     * ===========================================================*/
    private function readTikTokExcel(string $path, string $ext): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        
        $reader->setLoadSheetsOnly(['Reports']);

        $spreadsheet = $reader->load($path);

        $sheet = $spreadsheet->getSheetByName('Reports');
        if (!$sheet) {
            throw new \Exception('Sheet "Reports" tidak ditemukan.');
        }

        // Ambil LANGSUNG cell (O(1))
        $timePeriodCell = trim((string) $sheet->getCell('F2')->getValue());
        $settlementCell = trim((string) $sheet->getCell('F5')->getValue());

        $periodeStart = '';
        $periodeEnd   = '';

        if ($timePeriodCell) {
            $dates = explode('-', $timePeriodCell);
            if (count($dates) === 2) {
                $periodeStart = $this->parseTikTokDate(trim($dates[0]));
                $periodeEnd   = $this->parseTikTokDate(trim($dates[1]));
            }
        }

        $totalSettlement = 0;
        if ($settlementCell) {
            $value = preg_replace('/[^\d.-]/', '', $settlementCell);
            $totalSettlement = (float) $value;
        }

        return [
            'total_settlement' => $totalSettlement,
            'periode_start'    => $periodeStart,
            'periode_end'      => $periodeEnd,
        ];
    }


    /* ===========================================================
     *  BACA CSV TIKTOK
     * ===========================================================*/
    private function readTikTokCSV(string $path): array
    {
        $totalSettlement = 0;
        $periodeStart = '';
        $periodeEnd = '';

        $file = fopen($path, 'r');
        if ($file) {
            while (($line = fgetcsv($file)) !== false) {
                if (empty($line[0])) continue;

                // Cari "Time period:"
                if (stripos($line[0], 'Time period:') !== false && isset($line[2])) {
                    $dateRange = trim($line[2]);
                    $dates = explode('-', $dateRange);
                    if (count($dates) === 2) {
                        $periodeStart = $this->parseTikTokDate(trim($dates[0]));
                        $periodeEnd = $this->parseTikTokDate(trim($dates[1]));
                    }
                }

                // Cari "Total settlement amount"
                if (stripos($line[0], 'Total settlement amount') !== false && isset($line[2])) {
                    $value = trim($line[2]);
                    $value = preg_replace('/[^\d.-]/', '', $value);
                    $totalSettlement = floatval($value);
                    break;
                }
            }
            fclose($file);
        }

        return [
            'total_settlement' => $totalSettlement,
            'periode_start' => $periodeStart,
            'periode_end' => $periodeEnd
        ];
    }

    /* ===========================================================
     *  BACA DENGAN CARA SEDERHANA (FALLBACK)
     * ===========================================================*/
    private function readTikTokSimple(string $path): array
    {
        $totalSettlement = 0;
        $periodeStart = '';
        $periodeEnd = '';

        // Baca file sebagai teks
        $content = file_get_contents($path);

        // Cari "Time period:"
        if (preg_match('/Time period:.*?(\d{4}[\/\-]\d{2}[\/\-]\d{2}\s*[-\u2013]\s*\d{4}[\/\-]\d{2}[\/\-]\d{2})/i', $content, $timeMatches)) {
            $dateRange = $timeMatches[1];
            $dates = preg_split('/[\-\u2013]/', $dateRange); // Support untuk dash biasa dan en dash
            if (count($dates) === 2) {
                $periodeStart = $this->parseTikTokDate(trim($dates[0]));
                $periodeEnd = $this->parseTikTokDate(trim($dates[1]));
            }
        }

        // Cari "Total settlement amount"
        if (preg_match('/Total settlement amount.*?([\d,]+(\.\d+)?)/i', $content, $settlementMatches)) {
            $value = $settlementMatches[1];
            $value = str_replace([',', ' '], '', $value);
            $totalSettlement = floatval($value);
        }

        return [
            'total_settlement' => $totalSettlement,
            'periode_start' => $periodeStart,
            'periode_end' => $periodeEnd
        ];
    }

    /* ===========================================================
     *  PARSE TANGGAL TIKTOK
     * ===========================================================*/
    private function parseTikTokDate(string $dateStr): string
    {
        // Format TikTok: "2025/06/30", "2025-06-30", atau "30/06/2025"
        $dateStr = trim($dateStr);

        // Normalisasi separator
        $dateStr = str_replace('/', '-', $dateStr);

        // Coba berbagai format
        $formats = [
            'Y-m-d',  // 2025-06-30
            'd-m-Y',  // 30-06-2025
            'Y/m/d',  // 2025/06/30
            'd/m/Y',  // 30/06/2025
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateStr);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        // Coba dengan strtotime sebagai fallback
        $timestamp = strtotime($dateStr);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return '';
    }
}

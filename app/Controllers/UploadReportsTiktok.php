<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use ZipArchive;

class UploadReportsTiktok extends BaseController
{
    protected $uploadLimit = 2000;

    public function uploadForm()
    {
        return view('tiktok/upload_form', ['uploadLimit' => $this->uploadLimit]);
    }

    public function uploadProcess()
    {
        helper('text');

        $file = $this->request->getFile('file');
        $hargaModal = (float) $this->request->getPost('harga_modal', FILTER_SANITIZE_NUMBER_FLOAT);

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid atau tidak diupload.');
        }

        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format file tidak didukung (csv / xls / xlsx).');
        }

        $uploadPath = WRITEPATH . 'uploads/';
        if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

        $filename = time() . '_' . $file->getRandomName();
        $file->move($uploadPath, $filename);
        $filePath = $uploadPath . $filename;

        $rows = [];

        try {
            if ($ext === 'csv') {
                $rows = $this->readCsvUltra($filePath);
            } else {
                $rows = $this->readXlsxUltra($filePath);
            }

            if (empty($rows)) {
                throw new \Exception('Data TikTok tidak ditemukan atau header tidak cocok.');
            }

            // ================= AUTO FALLBACK COLUMNS =================
            $db = \Config\Database::connect();
            $builder = $db->table('tiktok_transactions');

            $fields = array_map('strtolower', $db->getFieldNames('tiktok_transactions'));

            $dateColumn = null;
            if (in_array('tanggal', $fields)) {
                $dateColumn = 'tanggal';
            } elseif (in_array('settled_at', $fields)) {
                $dateColumn = 'settled_at';
            } elseif (in_array('created_at', $fields)) {
                $dateColumn = 'created_at';
            }

            $count = 0;

            foreach ($rows as $r) {
                if (empty($r['order_id'])) continue;

                $profit = ($r['settlement'] ?? 0) - ($r['fees'] ?? 0) - $hargaModal;

                $data = [
                    'order_id'       => $r['order_id'],
                    'currency'       => 'IDR',
                    'settlement'     => $r['settlement'] ?? 0,
                    'fees'           => $r['fees'] ?? 0,
                    'harga_modal'    => $hargaModal,
                    'profit'         => $profit,
                    'created_input'  => date('Y-m-d H:i:s')
                ];

                if ($dateColumn) {
                    if (!empty($r['tanggal'])) {
                        $ts = strtotime($r['tanggal']);
                        if ($ts !== false) {
                            $data[$dateColumn] = $dateColumn === 'tanggal'
                                ? date('Y-m-d', $ts)
                                : date('Y-m-d H:i:s', $ts);
                        } else {
                            $data[$dateColumn] = $r['tanggal'];
                        }
                    }
                }

                $builder->insert($data);
                $count++;
            }
        } catch (\Throwable $e) {
            if (file_exists($filePath)) {
                usleep(200000);
                @unlink($filePath);
            }

            return redirect()->back()->with('error', 'Gagal baca file: ' . $e->getMessage());
        }

        if (file_exists($filePath)) {
            usleep(200000);
            @unlink($filePath);
        }

        return redirect()->to('/transaksi-tiktok')->with('success', "✅ Berhasil import {$count} baris.");
    }

    /* ---------------- CSV ---------------- */
    private function readCsvUltra(string $path): array
    {
        $rows = [];
        $file = new \SplFileObject($path);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);

        $header = null;
        $headerMap = [];

        foreach ($file as $index => $line) {
            if ($index === 0) {
                $header = $line;
                $headerMap = $this->buildHeaderMap($header);

                if (!$this->isValidTiktokHeader($headerMap)) {
                    throw new \Exception('File bukan laporan TikTok (CSV header tidak cocok).');
                }
                continue;
            }

            if (!isset($line[0])) continue;

            $assoc = $this->mapRowByHeader($line, $headerMap);
            if (empty($assoc['order_id'])) continue;

            $rows[] = $assoc;
            if (count($rows) >= $this->uploadLimit) break;
        }

        return $rows;
    }

    /* ---------------- XLSX ---------------- */
    private function readXlsxUltra(string $path): array
    {
        $rows = [];

        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new \Exception('Gagal membuka file XLSX');
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');

        if (!$sheetXml) {
            $zip->close();
            throw new \Exception('Worksheet sheet1 tidak ditemukan di XLSX');
        }

        $sharedStrings = [];
        if ($sharedXml) {
            preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $sharedXml, $m);
            $sharedStrings = $m[1] ?? [];
        }

        preg_match_all('/<row[^>]*>(.*?)<\/row>/s', $sheetXml, $rowsXml);

        if (empty($rowsXml[1])) {
            $zip->close();
            throw new \Exception('Tidak ada baris pada sheet1');
        }

        $header = null;
        $headerMap = [];

        foreach ($rowsXml[1] as $rIndex => $rowXml) {
            preg_match_all('/<c[^>]*>(.*?)<\/c>/s', $rowXml, $cells);

            $line = [];
            foreach ($cells[1] as $c) {
                if (preg_match('/<v>(.*?)<\/v>/s', $c, $v)) {
                    $value = $v[1];
                    $line[] = isset($sharedStrings[(int)$value]) ? $sharedStrings[(int)$value] : $value;
                } else {
                    $line[] = '';
                }
            }

            if ($rIndex === 0) {
                $header = $line;
                $headerMap = $this->buildHeaderMap($header);

                if (!$this->isValidTiktokHeader($headerMap)) {
                    $zip->close();
                    throw new \Exception('File bukan laporan TikTok (header tidak cocok)');
                }

                continue;
            }

            $assoc = $this->mapRowByHeader($line, $headerMap);
            if (empty($assoc['order_id'])) continue;

            $rows[] = $assoc;
            if (count($rows) >= $this->uploadLimit) break;
        }

        $zip->close();
        return $rows;
    }

    /* ---------------- HEADER ---------------- */

    private function buildHeaderMap(array $header): array
    {
        $map = [];
        foreach ($header as $i => $h) {
            $k = $this->normalize((string)$h);
            if ($k !== '') $map[$k] = $i;
        }
        return $map;
    }

    private function normalize(string $h): string
    {
        $s = strtolower(trim($h));
        $s = preg_replace('/^\xEF\xBB\xBF/', '', $s);
        $s = preg_replace('/[^a-z0-9]/', '', $s);
        $s = str_replace('utc', '', $s);
        return $s;
    }

    private function isValidTiktokHeader(array $map): bool
    {
        $hasOrder = isset($map['orderid']);
        $hasSettlement = isset($map['totalsettlementamount']) || isset($map['totalsettlement']);
        $hasDate = isset($map['ordercreatedtime']) || isset($map['ordercreated']);

        return ($hasOrder && $hasSettlement && $hasDate);
    }

    private function mapRowByHeader(array $row, array $map): array
    {
        $get = function (array $keys) use ($row, $map) {
            foreach ($keys as $k) {
                $nk = $this->normalize($k);
                if (isset($map[$nk])) {
                    return $row[$map[$nk]] ?? null;
                }
            }
            return null;
        };

        return [
            'order_id'   => $get(['Order ID', 'Order/adjustment ID']),
            'tanggal'    => $get(['Order created time(UTC)', 'Order created', 'Order date']),
            'settlement' => $this->sanitizeNumber($get(['Total settlement amount', 'Total settlement'])),
            'fees'       => $this->sanitizeNumber($get(['Total fees', 'Shipping cost', 'Biaya'])),
        ];
    }

    private function sanitizeNumber($v)
    {
        if ($v === null || $v === '') return 0;
        $s = (string)$v;
        $s = str_replace(['Rp', 'IDR', ' ', ',', "\u{00A0}"], '', $s);
        $s = preg_replace('/[^\d\.\-]/', '', $s);
        return $s === '' ? 0 : (float)$s;
    }
}

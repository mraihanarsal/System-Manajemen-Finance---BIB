<?php 
namespace App\Controllers;

use App\Models\PengeluaranModel;
use App\Models\KategoriPengeluaranModel;
use CodeIgniter\API\ResponseTrait;

class Pengeluaran extends BaseController
{
    use ResponseTrait;

    protected $pengeluaranModel;
    protected $kategoriModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
        $this->kategoriModel = new KategoriPengeluaranModel();
    }

    // Halaman utama
    public function index()
    {
        $kategori = $this->kategoriModel->where('is_active', 1)->findAll();
        
        return view('pengeluaran/index', [
            'kategori' => $kategori
        ]);
    }

    // Ambil semua data dengan pagination + filter
    public function getAll()
    {
        try {
            $page  = (int) ($this->request->getGet('page') ?? 1);
            $limit = (int) ($this->request->getGet('limit') ?? 10);
            
            // Filter Params
            $start = $this->request->getGet('start'); // YYYY-MM-DD
            $end   = $this->request->getGet('end');   // YYYY-MM-DD
            $cat   = $this->request->getGet('kategori_id');
            $year  = $this->request->getGet('year');  // New Year Param

            // DEBUG: Log params to verify what is received
            // file_put_contents('debug_filter.txt', print_r($this->request->getGet(), true));

            // Normalize: If Year is present, FORCE usage of its range, overriding client params
            if (!empty($year)) {
                 $start = $year . '-01-01';
                 $end   = $year . '-12-31';
            }

            $offset = ($page - 1) * $limit;

            // Use Explicit Builder to ensure strict control over the query
            // and avoid any Model-level interference or state retention
            $builder = $this->pengeluaranModel->builder(); 
            
            $builder->select('pengeluaran.*, kategori_pengeluaran.nama as nama_kategori, kategori_pengeluaran.kode as kode_kategori');
            $builder->join('kategori_pengeluaran', 'kategori_pengeluaran.id = pengeluaran.kategori_id', 'left');

            // Apply Date Range Filter with Raw SQL
            if (!empty($start) && !empty($end)) {
                $s = $this->pengeluaranModel->db->escapeString($start);
                $e = $this->pengeluaranModel->db->escapeString($end);
                $builder->where("pengeluaran.periode BETWEEN '$s' AND '$e'");
            }

            if (!empty($cat)) {
                $builder->where('pengeluaran.kategori_id', $cat);
            }

            // Count total (keep compiled select for next query)
            $total = $builder->countAllResults(false);
            
            // Sort Params
            $sortBy = $this->request->getGet('sort_by') ?? 'periode';
            $order  = $this->request->getGet('order') ?? 'DESC';

            // Whitelist sort columns
            $allowedSorts = ['periode', 'jumlah', 'deskripsi', 'nama_kategori'];
            if (!in_array($sortBy, $allowedSorts)) {
                $sortBy = 'periode';
            }
            
            // Normalize sorting for joined columns if needed
            if ($sortBy === 'nama_kategori') {
                 $sortColumn = 'kategori_pengeluaran.nama';
            } elseif ($sortBy === 'jumlah') {
                 $sortColumn = 'pengeluaran.jumlah';
            } elseif ($sortBy === 'deskripsi') {
                 $sortColumn = 'pengeluaran.deskripsi';
            } else {
                 $sortColumn = 'pengeluaran.periode';
            }

            // Get data
            $data = $builder->orderBy($sortColumn, $order)
                            ->orderBy('pengeluaran.created_at', 'DESC') // Secondary sort
                            ->get($limit, $offset)
                            ->getResultArray();

            $totalPages = ceil($total / $limit);

            return $this->response->setJSON([
                'status'      => true,
                'data'        => $data,
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'totalPages'  => $totalPages
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in Pengeluaran::getAll: '.$e->getMessage());
            return $this->response->setJSON([
                'status' => false,
                'error'  => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    // Get Summary Total
    public function getTotal()
    {
        try {
            $start = $this->request->getGet('start');
            $end   = $this->request->getGet('end');
            $year  = $this->request->getGet('year');
            
            $builder = $this->pengeluaranModel
                ->selectSum('jumlah');

            // Normalize: If Year is present, FORCE usage of its range
            if (!empty($year)) {
                 $start = $year . '-01-01';
                 $end   = $year . '-12-31';
            }

            if (!empty($start) && !empty($end)) {
                $s = $this->pengeluaranModel->db->escapeString($start);
                $e = $this->pengeluaranModel->db->escapeString($end);
                $builder->where("periode BETWEEN '$s' AND '$e'");
            }

            $total = $builder->get()->getRow()->jumlah ?? 0;

            return $this->response->setJSON([
                'status' => true,
                'total'  => (float)$total
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'error'  => $e->getMessage()
            ]);
        }
    }

    // Tambah data
    public function tambah()
    {
        // Validasi input
        $rules = [
            'periode'     => 'required|valid_date',
            'kategori_id' => 'required|integer',
            'nominal'     => 'required',
            'deskripsi'   => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
             return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            $nominalRaw = $this->request->getPost('nominal');
            // Bersihkan format Rp (e.g. "1.500.000" -> "1500000")
            $jumlah = (float) preg_replace('/[^\d]/', '', $nominalRaw);

            if ($jumlah <= 0) {
                return $this->fail('Nominal harus lebih dari 0.');
            }

            $currentUserId = session()->get('user_id');
            // Jika user_id tidak ada di session (misal logout/expire), set 0 atau error.
            // Sesuai schema, created_by BIGINT NOT NULL. 
            $userId = $currentUserId ? $currentUserId : 1; // Default to ID 1 or system user if needed

            $data = [
                'periode'     => $this->request->getPost('periode'),
                'kategori_id' => $this->request->getPost('kategori_id'),
                'deskripsi'   => $this->request->getPost('deskripsi'),
                'jumlah'      => $jumlah,
                'created_by'  => $userId, 
                'created_at'  => date('Y-m-d H:i:s')
            ];

            if ($this->pengeluaranModel->insert($data)) {
                return $this->respond([
                    'status' => true,
                    'pesan'  => 'Data berhasil ditambahkan.'
                ]);
            } else {
                return $this->fail('Gagal menyimpan data ke database.');
            }
        } catch (\Exception $e) {
            return $this->fail('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Ubah data
    public function ubah($id = null)
    {
        if (!$id) return $this->fail('ID tidak ditemukan.');

        // Validation for Edit
        $rules = [
            'periode'     => 'required|valid_date',
            'kategori_id' => 'required|integer',
            'nominal'     => 'required',
            'deskripsi'   => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
             return $this->failValidationErrors($this->validator->getErrors());
        }

        try {
            $nominalRaw = $this->request->getPost('nominal');
            $jumlah = (float) preg_replace('/[^\d]/', '', $nominalRaw);

            if ($jumlah <= 0) {
                return $this->fail('Nominal harus lebih dari 0.');
            }

            $data = [
                'periode'     => $this->request->getPost('periode'),
                'kategori_id' => $this->request->getPost('kategori_id'),
                'deskripsi'   => $this->request->getPost('deskripsi'),
                'jumlah'      => $jumlah,
            ];

            if ($this->pengeluaranModel->update($id, $data)) {
                return $this->respond(['status' => true, 'pesan' => 'Data berhasil diubah']);
            }

            return $this->fail('Gagal mengubah data');
        } catch (\Exception $e) {
            return $this->fail('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Hapus data
    public function hapus($id = null)
    {
        if (!$id) return $this->fail('ID tidak ditemukan.');

        if ($this->pengeluaranModel->delete($id)) {
            return $this->respond(['status' => true, 'pesan' => 'Data berhasil dihapus']);
        }

        return $this->fail('Gagal menghapus data');
    }
}
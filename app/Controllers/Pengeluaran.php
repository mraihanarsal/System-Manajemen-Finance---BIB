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

            $offset = ($page - 1) * $limit;

            // Build query
            $builder = $this->pengeluaranModel
                ->select('pengeluaran.*, kategori_pengeluaran.nama as nama_kategori, kategori_pengeluaran.kode as kode_kategori')
                ->join('kategori_pengeluaran', 'kategori_pengeluaran.id = pengeluaran.kategori_id', 'left');

            if (!empty($start) && !empty($end)) {
                $builder->where('periode >=', $start)
                        ->where('periode <=', $end);
            }
            // Fallback: jika tidak ada filter, default bulan ini? atau show all?
            // User request suggests specific period logic, let's allow all if empty or default to current month client-side.

            if (!empty($cat)) {
                $builder->where('kategori_id', $cat);
            }

            // Clone builder for count
            $countBuilder = clone $builder;
            $total = $countBuilder->countAllResults();
            
            // Get data
            $data = $builder->orderBy('periode', 'DESC')
                            ->orderBy('created_at', 'DESC')
                            ->findAll($limit, $offset);

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
            
            $builder = $this->pengeluaranModel
                ->selectSum('jumlah');

            if (!empty($start) && !empty($end)) {
                $builder->where('periode >=', $start)
                        ->where('periode <=', $end);
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
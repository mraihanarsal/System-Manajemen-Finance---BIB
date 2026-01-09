<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{

    protected $pengeluaranModel;
    protected $tiktokModel;
    protected $transaksiModel;
    protected $tokoModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->pengeluaranModel = new \App\Models\PengeluaranModel();
        $this->tiktokModel = new \App\Models\TiktokTransaksiModel();
        $this->transaksiModel = new \App\Models\TransaksiModel();
        $this->tokoModel = new \App\Models\TokoModel();
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $currentYear = date('Y');
        $currentMonth = date('m');

        // --- PENDAPATAN (Income) ---
        // 1. Tiktok
        $tiktokMonthly = $this->tiktokModel->where('YEAR(periode_start)', $currentYear)
                                           ->where('MONTH(periode_start)', $currentMonth)
                                           ->selectSum('settlement')->first()['settlement'] ?? 0;
        $tiktokYearly = $this->tiktokModel->where('YEAR(periode_start)', $currentYear)
                                          ->selectSum('settlement')->first()['settlement'] ?? 0;

        // 2. Shopee (via TransaksiModel / upload_reports check)
        // Note: TransaksiModel::getGlobalPendapatanHistory('shopee') uses upload_reports table logic internally
        $shopeeMonthlyArr = $this->transaksiModel->getGlobalPendapatanHistory('shopee'); // Returns array of years/months
        // Filter for current month in PHP or query directly if possible. 
        // Let's query directly for efficiency if model logic allows, or use the existing method pattern.
        // The existing method groups by month. Let's do raw query or new method? 
        // Actually, TransaksiModel has logic for 'shopee' vs others. 
        // Let's use database builder to be safe and direct if methods are complex.
        
        // Shopee (Direct from upload_reports based on TransaksiModel logic)
        $db = \Config\Database::connect();
        $shopeeMonthly = $db->table('upload_reports')
                            ->where('YEAR(periode_awal)', $currentYear)
                            ->where('MONTH(periode_awal)', $currentMonth)
                            ->selectSum('total_penghasilan')->get()->getRow()->total_penghasilan ?? 0;
        
        $shopeeYearly = $db->table('upload_reports')
                           ->where('YEAR(periode_awal)', $currentYear)
                           ->selectSum('total_penghasilan')->get()->getRow()->total_penghasilan ?? 0;

        // 3. Zefatex (platform_transactions table with platform='zefatex' OR zefatex_transactions table??)
        // User's code has ZefatexController using ZefatexModel (zefatex_transactions).
        // BUT TransaksiModel has 'zefatex' platform logic too.
        // Let's check ZefatexModel... it sums 'total_amount'.
        $zefatexModel = new \App\Models\ZefatexModel();
        $zefatexMonthly = $zefatexModel->where('YEAR(transaction_date)', $currentYear)
                                       ->where('MONTH(transaction_date)', $currentMonth)
                                       ->selectSum('total_amount')->first()['total_amount'] ?? 0;
        $zefatexYearly = $zefatexModel->where('YEAR(transaction_date)', $currentYear)
                                      ->selectSum('total_amount')->first()['total_amount'] ?? 0;

        $totalIncomeMonthly = $tiktokMonthly + $shopeeMonthly + $zefatexMonthly;
        $totalIncomeYearly = $tiktokYearly + $shopeeYearly + $zefatexYearly;

        // --- PENGELUARAN (Expenses) ---
        $expenseMonthly = $this->pengeluaranModel->where('YEAR(periode)', $currentYear)
                                                 ->where('MONTH(periode)', $currentMonth)
                                                 ->selectSum('jumlah')->first()['jumlah'] ?? 0;
        $expenseYearly = $this->pengeluaranModel->where('YEAR(periode)', $currentYear)
                                                ->selectSum('jumlah')->first()['jumlah'] ?? 0;

        // --- TOKO SAYA (Stores) ---
        // Only Shopee and Tiktok are typically counted in master_toko if Zefatex is just 1 manual one?
        // User said: "toko saya belum diketahui jumlahnya berapa dari semua platform (kalo zefatex memang cuman 1 aja)"
        $shopeeStores = $this->tokoModel->where('platform', 'shopee')->countAllResults();
        $tiktokStores = $this->tokoModel->where('platform', 'tiktok')->countAllResults();
        $zefatexStores = 1; 
        $totalStores = $shopeeStores + $tiktokStores + $zefatexStores;


        $data = [
            'title' => 'Dashboard',
            'user' => $this->getUserData(),
            'income' => [
                'monthly' => $totalIncomeMonthly,
                'yearly' => $totalIncomeYearly
            ],
            // We don't have separate expense card in original UI, but user asked for "pendapatan tahunan... bulanan... jadi otomatis terupdate jika user melakukan aksi update pemasukan atau pengeluarannya".
            // Wait, the UI has "Pendapatan (Bulanan)", "Pendapatan (Tahunan)".
            // It does NOT have an "Expense" card explicitly shown in my view_file output of `_cards_earning.php`.
            // Ah, I should check if I need to ADD an expense card or if "Pengeluaran" is just for the graph?
            // User said: "pada bagian pengeluaran belum ada riwayat pengeluarannya ini harus dinamis" which refers to the Pengeluaran page likely.
            // But the dashboard should probably reflect Net Income? Or just Gross? 
            // Usually "Pendapatan" is Revenue. 
            // I will pass expense data just in case I want to calculate Net.
            'expense' => [
                'monthly' => $expenseMonthly,
                'yearly' => $expenseYearly
            ],
            'stores' => [
                'count' => $totalStores
            ],
            'users_count' => $this->userModel->countAllResults()
        ];
        return view('dashboard/index', $data);
    }
    
    public function profile()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $userData = $this->getUserData();
        $data = [
            'title' => 'Profile',
            'user' => [
                'id' => $userData['id'],
                'nama' => $userData['nama'],
                'username' => $userData['username'] ?? $userData['nama'], // Tambahkan username
                'email' => $userData['email'],
                'role' => $userData['role'],
                'foto' => $userData['foto'],
                'is_master' => $userData['is_master'],
                'status' => 'active'
            ]
        ];
        return view('dashboard/profile', $data);
    }

    public function update_profile()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $userId = session()->get('user_id');
        $nama = $this->request->getPost('nama');
        $username = $this->request->getPost('username');
        
        // Validation Rules
        $rules = [
            'nama' => 'required|min_length[3]',
            'username' => "required|min_length[3]|is_unique[users.username,id,{$userId}]" // Unique check except current user
        ];

        if (!$this->validate($rules)) {
            // Get first error message
            $errors = $this->validator->getErrors();
            $msg = reset($errors);
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }

        try {
            // Update database
            $this->userModel->update($userId, [
                'nama' => $nama,
                'username' => $username
            ]);
            
            // Update session
            session()->set('nama', $nama);
            session()->set('username', $username);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Profile berhasil diperbarui']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
        }
    }

    public function upload_foto()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $file = $this->request->getFile('foto');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Hanya file gambar yang diizinkan (JPEG, PNG, GIF)']);
            }
            
            // Validasi ukuran file (max 2MB)
            if ($file->getSize() > 2097152) {
                return $this->response->setJSON(['success' => false, 'message' => 'Ukuran file maksimal 2MB']);
            }
            
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/profiles', $newName);
            
            // Update database
            $this->userModel->update(session()->get('user_id'), [
                'foto' => $newName
            ]);
            
            // Update session
            session()->set('foto', $newName);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Foto berhasil diupload']);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid']);
    }
    
    public function ganti_password()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authenticated']);
        }

        $password = $this->request->getPost('password');
        
        if (strlen($password) < 6) {
            return $this->response->setJSON(['success' => false, 'message' => 'Password minimal 6 karakter']);
        }
        
        // Update password di database
        $this->userModel->update(session()->get('user_id'), [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
        
        return $this->response->setJSON(['success' => true, 'message' => 'Password berhasil diubah']);
    }

    public function kelola_pengguna()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        if (!session()->get('is_master') && session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
        $data = [
            'title' => 'Kelola Pengguna',
            'users' => $this->userModel->getUsersExceptMaster(),
            'user' => $this->getUserData()
        ];
        return view('dashboard/kelola_pengguna', $data);
    }

    public function login_logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }

    private function getUserData()
    {
        if (session()->get('isLoggedIn')) {
            return [
                'id' => session()->get('user_id'),
                'nama' => session()->get('nama'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
                'foto' => session()->get('foto'),
                'is_master' => session()->get('is_master')
            ];
        }
    }
}

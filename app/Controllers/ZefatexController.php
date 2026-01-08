<?php

namespace App\Controllers;

use App\Models\ZefatexModel;

class ZefatexController extends BaseController
{
    protected $zefatexModel;
    protected $db;

    public function __construct()
    {
        $this->zefatexModel = new ZefatexModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $keyword = $this->request->getGet('search');
        
        $data = [
            'invoices' => $this->zefatexModel->getInvoices($keyword)->findAll(),
            // 'pager' => $this->zefatexModel->pager, // Not executing pager anymore
            'totalRevenue' => $this->zefatexModel->getTotalRevenue($keyword),
            'keyword' => $keyword
        ];

        return view('zefatex/index', $data);
    }

    // FORM INPUT PAGE
    public function create()
    {
        return view('zefatex/create');
    }

    // PROCESS SAVE
    public function store()
    {
        $isAjax = $this->request->isAJAX();

        $rules = [
            'invoice_number' => 'required',
            'customer_name' => 'required',
            'transaction_date' => 'required',
            // Image is required for new entries unless handled otherwise
            'image' => 'uploaded[image]|max_size[image,10240]|is_image[image]' 
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $errors])->setStatusCode(400); // Bad Request
            }
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Upload Image
        $file = $this->request->getFile('image');
        $fileName = '';
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move('uploads/zefatex', $fileName);
        }

        // Transaction Data
        $transactionData = [
            'invoice_number' => $this->request->getPost('invoice_number'),
            'customer_name' => $this->request->getPost('customer_name'),
            'transaction_date' => $this->request->getPost('transaction_date'),
            'description'   => $this->request->getPost('note'),
            'image_path'    => $fileName,
            'total_amount'  => 0 // Will be calculated from items
        ];

        // START TRANSACTION
        $this->db->transStart();

        try {
            // 1. Insert Header
            $this->zefatexModel->insert($transactionData);
            $transactionId = $this->zefatexModel->getInsertID();

            // 2. Insert Items
            $items_desc = $this->request->getPost('item_desc');
            $items_qty = $this->request->getPost('item_qty');
            $items_price = $this->request->getPost('item_price');
            
            $totalAmount = 0;

            if ($items_desc && is_array($items_desc)) {
                $batchData = [];
                foreach ($items_desc as $key => $desc) {
                    // if (empty($desc)) continue; // Allow empty desc? No, usually required.
                    
                    $qty = isset($items_qty[$key]) ? (float)$items_qty[$key] : 1;
                    $price = isset($items_price[$key]) ? (float)$items_price[$key] : 0;
                    $amount = $qty * $price;
                    $totalAmount += $amount;

                    $batchData[] = [
                        'transaction_id' => $transactionId,
                        'description' => $desc ?: '-',
                        'qty' => $qty,
                        'price' => $price,
                        'amount' => $amount
                    ];
                }
                
                if (!empty($batchData)) {
                    $builder = $this->db->table('zefatex_transaction_items');
                    $builder->insertBatch($batchData);
                }
            }

            // 3. Update Total Amount
            $this->zefatexModel->update($transactionId, ['total_amount' => $totalAmount]);

            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                throw new \Exception('Transaction failed');
            }

            if ($isAjax) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Transaksi berhasil']);
            }
            return redirect()->to('/zefatex')->with('success', 'Transaksi berhasil disimpan.');

        } catch (\Exception $e) {
            $this->db->transRollback();
            $dbError = $this->db->error();
            $msg = $e->getMessage() . ' ' . json_encode($dbError);
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(500);
            }
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $msg);
        }
    }

    // EDIT PAGE
    public function edit($id)
    {
        $trx = $this->zefatexModel->find($id);
        if (!$trx) {
            return redirect()->to('/zefatex')->with('error', 'Transaksi tidak ditemukan');
        }

        // Get Items
        $items = $this->db->table('zefatex_transaction_items')->where('transaction_id', $id)->get()->getResultArray();

        $data = [
            'trx' => $trx,
            'items' => $items
        ];

        return view('zefatex/edit', $data);
    }

    // UPDATE PROCESS
    public function update($id)
    {
        $trx = $this->zefatexModel->find($id);
        if (!$trx) {
            return redirect()->to('/zefatex')->with('error', 'Data tidak ditemukan');
        }

        $rules = [
            'invoice_number' => 'required',
            'customer_name' => 'required',
            'transaction_date' => 'required',
             // Image optional on update
            'image' => 'max_size[image,10240]|is_image[image]' 
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle Image
        $file = $this->request->getFile('image');
        $fileName = $trx['image_path']; // Keep old one by default

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old image if exists
            if ($trx['image_path'] && file_exists('uploads/zefatex/' . $trx['image_path'])) {
                unlink('uploads/zefatex/' . $trx['image_path']);
            }
            $fileName = $file->getRandomName();
            $file->move('uploads/zefatex', $fileName);
        }

        $transactionData = [
            'invoice_number' => $this->request->getPost('invoice_number'),
            'customer_name' => $this->request->getPost('customer_name'),
            'transaction_date' => $this->request->getPost('transaction_date'),
            'description'   => $this->request->getPost('note'),
            'image_path'    => $fileName,
            // total_amount updated after items
        ];

        $this->db->transStart();

        // 1. Update Header
        $this->zefatexModel->update($id, $transactionData);

        // 2. Delete Old Items (Simple Strategy: Delete All & Re-insert)
        $this->db->table('zefatex_transaction_items')->where('transaction_id', $id)->delete();

        // 3. Insert New Items
        $items_desc = $this->request->getPost('item_desc');
        $items_qty = $this->request->getPost('item_qty');
        $items_price = $this->request->getPost('item_price');
        
        $totalAmount = 0;

        if ($items_desc && is_array($items_desc)) {
            $batchData = [];
            foreach ($items_desc as $key => $desc) {
                // if (empty($desc)) continue;
                
                $qty = isset($items_qty[$key]) ? (float)$items_qty[$key] : 1;
                $price = isset($items_price[$key]) ? (float)$items_price[$key] : 0;
                $amount = $qty * $price;
                $totalAmount += $amount;

                $batchData[] = [
                    'transaction_id' => $id,
                    'description' => $desc ?: '-',
                    'qty' => $qty,
                    'price' => $price,
                    'amount' => $amount
                ];
            }
            
            if (!empty($batchData)) {
                $this->db->table('zefatex_transaction_items')->insertBatch($batchData);
            }
        }

        // 4. Update Total
        $this->zefatexModel->update($id, ['total_amount' => $totalAmount]);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
             return redirect()->back()->withInput()->with('error', 'Gagal update transaksi.');
        }

        return redirect()->to('/zefatex')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function delete($id)
    {
         // Find data to delete image
         $trx = $this->zefatexModel->find($id);
         if ($trx) {
             $path = 'uploads/zefatex/' . $trx['image_path'];
             if (!empty($trx['image_path']) && file_exists($path)) {
                 unlink($path);
             }
             // Also delete items (handled by DB Cascade if set, but safe to do here if not)
             // $this->db->table('zefatex_transaction_items')->where('transaction_id', $id)->delete();
         }

         $this->zefatexModel->delete($id);
         return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}

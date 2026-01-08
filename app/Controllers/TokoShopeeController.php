<?php

namespace App\Controllers;

use App\Models\TokoModel;

class TokoShopeeController extends BaseController
{
    protected $tokoModel;

    public function __construct()
    {
        $this->tokoModel = new TokoModel();
    }

    public function index()
    {
        $data = [
            'toko' => $this->tokoModel
                ->where('platform', 'shopee')
                ->orderBy('id_toko', 'DESC')
                ->paginate(5, 'toko_shopee'),

            'pager' => $this->tokoModel->pager,
            'id_toko_auto' => $this->tokoModel->generateIdToko('shopee')
        ];

        return view('shopee/toko/index', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_toko'   => 'required',
            'nama_toko' => 'required|min_length[3]',
            'alamat'    => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'id_toko'   => $this->request->getPost('id_toko'),
            'nama_toko' => $this->request->getPost('nama_toko'),
            'platform'  => 'shopee',
            'alamat'    => $this->request->getPost('alamat'),
            'is_active' => 1,
            'created_by' => session()->get('username') ?? 'admin'
        ];

        try {
            $this->tokoModel->insert($data);
            return redirect()->to('/shopee/toko')->with('success', 'Toko berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan toko: ' . $e->getMessage());
        }
    }

    public function update($id_toko)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nama_toko' => 'required|min_length[3]',
            'alamat'    => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'nama_toko'  => $this->request->getPost('nama_toko'),
            'alamat'     => $this->request->getPost('alamat'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->tokoModel->update($id_toko, $data);

            session()->setFlashdata('success', 'Toko berhasil diupdate');
            return redirect()->to('/shopee/toko');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate toko: ' . $e->getMessage());
        }
    }

    public function activate($id_toko)
    {
        $this->tokoModel->update($id_toko, [
            'is_active' => 1
        ]);

        return redirect()->to('/shopee/toko')->with('success', 'Toko berhasil diaktifkan');
    }

    public function deactivate($id_toko)
    {
        $this->tokoModel->update($id_toko, [
            'is_active' => 0
        ]);

        return redirect()->to('/shopee/toko')->with('success', 'Toko berhasil dinonaktifkan');
    }
    public function delete($id_toko)
    {
        $this->tokoModel->delete($id_toko);
        return redirect()->to('/shopee/toko')->with('success', 'Toko berhasil dihapus');
    }
}

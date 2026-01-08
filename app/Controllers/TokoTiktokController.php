<?php

namespace App\Controllers;

use App\Models\TokoModel;

class TokoTiktokController extends BaseController
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
                ->where('platform', 'tiktok')
                ->orderBy('created_at', 'DESC')
                ->paginate(5, 'toko_tiktok'),

            'pager' => $this->tokoModel->pager,
            'id_tiktok_auto' => $this->tokoModel->generateIdToko('tiktok')
        ];

        return view('tiktok/toko/index', $data);
    }

    public function store()
    {
        $nama = trim($this->request->getPost('nama_toko'));

        if ($nama == '') {
            return redirect()->back()->with('error', 'Nama toko wajib diisi');
        }

        $data = [
            'id_toko'    => $this->tokoModel->generateIdToko('tiktok'),
            'nama_toko'  => $nama,
            'alamat'     => $this->request->getPost('alamat'),
            'platform'   => 'tiktok',
            'is_active'  => 1
        ];

        $this->tokoModel->insert($data);

        return redirect()->to('/tiktok/toko')->with('success', 'Toko TikTok berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->tokoModel->delete($id);
        return redirect()->to('/tiktok/toko')->with('success', 'Toko berhasil dihapus');
    }

    public function update($id)
    {
        $data = [
            'nama_toko' => $this->request->getPost('nama_toko'),
            'alamat'    => $this->request->getPost('alamat'),
        ];

        $this->tokoModel->update($id, $data);

        return redirect()->to('/tiktok/toko')->with('success', 'Toko TikTok berhasil diperbarui');
    }

    public function deactivate($id)
    {
        $this->tokoModel->deactivate($id);
        return redirect()->to('/tiktok/toko')->with('success', 'Toko berhasil dinonaktifkan');
    }

    public function activate($id)
    {
        $this->tokoModel->activate($id);
        return redirect()->to('/tiktok/toko')->with('success', 'Toko berhasil diaktifkan');
    }
}

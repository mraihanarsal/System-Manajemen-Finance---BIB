<?php

namespace App\Models;

use CodeIgniter\Model;

class ZefatexModel extends Model
{
    protected $table = 'zefatex_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'invoice_number',
        'customer_name',
        'transaction_date',
        'total_amount',
        'description',
        'image_path',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getInvoices($keyword = null)
    {
        if ($keyword) {
            $this->groupStart()
                 ->like('invoice_number', $keyword)
                 ->orLike('customer_name', $keyword)
                 ->groupEnd();
        }
        return $this->orderBy('transaction_date', 'DESC');
    }

    public function getTotalRevenue($keyword = null)
    {
         if ($keyword) {
            $this->groupStart()
                 ->like('invoice_number', $keyword)
                 ->orLike('customer_name', $keyword)
                 ->groupEnd();
        }
        return $this->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;
    }
}

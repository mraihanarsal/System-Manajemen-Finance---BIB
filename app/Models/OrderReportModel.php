<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderReportModel extends Model
{
    protected $table = 'order_reports';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id',
        'sku',
        'quantity',
        'total_settlement',
        'order_date',
        'platform',
        'raw_sheet'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
}

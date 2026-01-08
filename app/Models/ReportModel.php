<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table = 'upload_reports';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_toko',
        'report_code',
        'username',
        'nama_file',
        'periode_awal',
        'periode_akhir',
        'total_penghasilan',
        'tanggal_upload'
    ];
}

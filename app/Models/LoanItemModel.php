<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanItemModel extends Model
{
    protected $table            = 'loan_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'loan_id',
        'product_id',
        'quantity',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'loan_id'   => 'required|integer',
        'product_id'=> 'required|integer',
        'quantity'  => 'required|integer|greater_than_equal_to[1]',
    ];
}

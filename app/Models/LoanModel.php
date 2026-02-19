<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanModel extends Model
{
    protected $table            = 'loans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'borrower_name',
        'borrower_identifier',
        'borrower_unit',
        'contact',
        'loan_date',
        'due_date',
        'status',
        'notes',
        'ticket_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'borrower_name'      => 'required|min_length[3]|max_length[150]',
        'borrower_identifier'=> 'permit_empty|max_length[100]',
        'borrower_unit'      => 'permit_empty|max_length[150]',
        'contact'            => 'permit_empty|max_length[100]',
        'loan_date'          => 'required|valid_date',
        'due_date'           => 'permit_empty|valid_date',
        'status'             => 'in_list[requested,approved,distributed,cancelled]',
    ];

    public function withItems($id)
    {
        $loan = $this->find($id);
        if (!$loan) return null;
        $items = (new LoanItemModel())
            ->select('loan_items.*, products.name as product_name, products.sku')
            ->join('products', 'products.id = loan_items.product_id')
            ->where('loan_id', $id)
            ->findAll();
        $loan['items'] = $items;
        return $loan;
    }

    public function getListWithCounts()
    {
        return $this->select('*')->orderBy('created_at', 'DESC')->findAll();
    }
}

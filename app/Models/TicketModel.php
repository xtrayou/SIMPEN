<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table            = 'tickets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'subject',
        'type',
        'priority',
        'status',
        'requester_name',
        'requester_contact',
        'description',
        'related_loan_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'subject'         => 'required|min_length[3]|max_length[200]',
        'type'            => 'in_list[loan-request,restock,issue,other]',
        'priority'        => 'in_list[low,medium,high,urgent]',
        'status'          => 'in_list[open,in_progress,resolved,closed]',
        'requester_name'  => 'required|min_length[3]|max_length[150]',
    ];

    public function withComments($id)
    {
        $ticket = $this->find($id);
        if (!$ticket) return null;
        $comments = (new TicketCommentModel())
            ->where('ticket_id', $id)
            ->orderBy('created_at', 'ASC')
            ->findAll();
        $ticket['comments'] = $comments;
        return $ticket;
    }
}

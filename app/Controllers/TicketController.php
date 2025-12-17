<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\TicketCommentModel;

class TicketController extends BaseController
{
    protected $ticketModel;
    protected $commentModel;

    public function __construct()
    {
        $this->ticketModel = new TicketModel();
        $this->commentModel = new TicketCommentModel();
    }

    public function index()
    {
        $this->setPageData('Ticketing', 'Permintaan & isu ATK');
        $status = $this->request->getGet('status');
        $builder = $this->ticketModel->orderBy('created_at', 'DESC');
        if ($status) $builder->where('status', $status);
        $tickets = $builder->findAll();
        return $this->render('tickets/index', [
            'tickets' => $tickets,
            'status' => $status,
        ]);
    }

    public function create()
    {
        $this->setPageData('Buat Ticket', 'Ajukan permintaan/isu');
        return $this->render('tickets/create', [
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        $rules = [
            'subject' => 'required|min_length[3]|max_length[200]',
            'requester_name' => 'required|min_length[3]|max_length[150]',
            'type' => 'permit_empty|in_list[loan-request,restock,issue,other]',
            'priority' => 'permit_empty|in_list[low,medium,high,urgent]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'subject' => $this->request->getPost('subject'),
            'type' => $this->request->getPost('type') ?: 'loan-request',
            'priority' => $this->request->getPost('priority') ?: 'medium',
            'status' => 'open',
            'requester_name' => $this->request->getPost('requester_name'),
            'requester_contact' => $this->request->getPost('requester_contact'),
            'description' => $this->request->getPost('description')
        ];

        if ($this->ticketModel->insert($data)) {
            $this->setFlash('success', 'Ticket berhasil dibuat');
            return redirect()->to('/tickets');
        }

        $this->setFlash('error', 'Gagal membuat ticket');
        return redirect()->back()->withInput();
    }

    public function show($id)
    {
        $ticket = $this->ticketModel->withComments($id);
        if (!$ticket) {
            $this->setFlash('error', 'Ticket tidak ditemukan');
            return redirect()->to('/tickets');
        }
        $this->setPageData('Detail Ticket', 'Ticket #' . $id);
        return $this->render('tickets/show', [ 'ticket' => $ticket ]);
    }

    public function addComment($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['status' => false, 'message' => 'Invalid request'], 400);
        }
        $rules = [
            'author_name' => 'required|min_length[3]|max_length[150]',
            'comment' => 'required|min_length[1]'
        ];
        if (!$this->validate($rules)) {
            return $this->jsonResponse(['status' => false, 'errors' => $this->validator->getErrors()], 422);
        }
        $this->commentModel->insert([
            'ticket_id' => $id,
            'author_name' => $this->request->getPost('author_name'),
            'comment' => $this->request->getPost('comment')
        ]);
        return $this->jsonResponse(['status' => true, 'message' => 'Komentar ditambahkan']);
    }

    public function updateStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->jsonResponse(['status' => false, 'message' => 'Invalid request'], 400);
        }
        $status = $this->request->getPost('status');
        $allowed = ['open','in_progress','resolved','closed'];
        if (!in_array($status, $allowed)) {
            return $this->jsonResponse(['status' => false, 'message' => 'Status tidak valid'], 422);
        }
        $this->ticketModel->update($id, ['status' => $status]);
        return $this->jsonResponse(['status' => true, 'message' => 'Status diperbarui']);
    }
}

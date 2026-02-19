<?php

namespace App\Controllers;

use App\Models\LoanModel;
use App\Models\LoanItemModel;
use App\Models\ProductModel;
use App\Models\StockMovementModel;

class LoanController extends BaseController
{
    protected $loanModel;
    protected $loanItemModel;
    protected $productModel;
    protected $stockMovementModel;

    public function __construct()
    {
        $this->loanModel = new LoanModel();
        $this->loanItemModel = new LoanItemModel();
        $this->productModel = new ProductModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    public function index()
    {
        $this->setPageData('Permintaan ATK', 'Tracking permintaan ATK fakultas');

        $status = $this->request->getGet('status');
        $builder = $this->loanModel->orderBy('created_at', 'DESC');
        if ($status) {
            $builder->where('status', $status);
        }
        $loans = $builder->findAll();

        return $this->render('loans/index', [
            'loans' => $loans,
            'status' => $status,
        ]);
    }

    public function create()
    {
        $this->setPageData('Buat Permintaan', 'Form permintaan ATK');
        $products = $this->productModel->where('is_active', true)->orderBy('name', 'ASC')->findAll();
        return $this->render('loans/create', [
            'products' => $products,
            'validation' => \Config\Services::validation()
        ]);
    }

    public function store()
    {
        $rules = [
            'borrower_name' => 'required|min_length[3]|max_length[150]',
            'loan_date' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $loanId = $this->loanModel->insert([
                'borrower_name' => $this->request->getPost('borrower_name'),
                'borrower_identifier' => $this->request->getPost('borrower_identifier'),
                'borrower_unit' => $this->request->getPost('borrower_unit'),
                'contact' => $this->request->getPost('contact'),
                'loan_date' => $this->request->getPost('loan_date'),
                'due_date' => $this->request->getPost('due_date') ?: null,
                'status' => 'requested',
                'notes' => $this->request->getPost('notes'),
            ]);

            $productIds = (array) $this->request->getPost('product_id');
            $quantities = (array) $this->request->getPost('quantity');

            foreach ($productIds as $i => $pid) {
                if (!$pid || empty($quantities[$i])) continue;
                $this->loanItemModel->insert([
                    'loan_id' => $loanId,
                    'product_id' => (int)$pid,
                    'quantity' => (int)$quantities[$i],
                    'notes' => null,
                ]);
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan permintaan');
            }

            $this->setFlash('success', 'Permintaan ATK berhasil dibuat');
            return redirect()->to('/loans');
        } catch (\Throwable $e) {
            $db->transRollback();
            $this->setFlash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $loan = $this->loanModel->withItems($id);
        if (!$loan) {
            $this->setFlash('error', 'Permintaan tidak ditemukan');
            return redirect()->to('/loans');
        }

        $this->setPageData('Detail Permintaan', 'No: #' . $id);
        return $this->render('loans/show', ['loan' => $loan]);
    }

    public function approve($id)
    {
        $loan = $this->loanModel->find($id);
        if (!$loan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        if ($loan['status'] !== 'requested') return $this->jsonResponse(['status' => false, 'message' => 'Status tidak valid'], 400);
        $this->loanModel->update($id, ['status' => 'approved']);
        return $this->jsonResponse(['status' => true, 'message' => 'Permintaan disetujui']);
    }

    public function distribute($id)
    {
        $loan = $this->loanModel->find($id);
        if (!$loan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        if (!in_array($loan['status'], ['approved'])) return $this->jsonResponse(['status' => false, 'message' => 'Status tidak valid'], 400);

        $items = $this->loanItemModel->where('loan_id', $id)->findAll();
        $db = \Config\Database::connect();
        $db->transStart();
        try {
            foreach ($items as $item) {
                $this->stockMovementModel->createMovement([
                    'product_id' => $item['product_id'],
                    'type' => 'OUT',
                    'quantity' => $item['quantity'],
                    'notes' => 'Distribusi Permintaan ATK #' . $id,
                    'created_by' => 'LoanSystem'
                ]);
            }
            $this->loanModel->update($id, ['status' => 'distributed']);
            $db->transComplete();
            if ($db->transStatus() === false) throw new \Exception('Transaksi gagal');
            return $this->jsonResponse(['status' => true, 'message' => 'Barang telah didistribusikan']);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->jsonResponse(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cancel($id)
    {
        $loan = $this->loanModel->find($id);
        if (!$loan) return $this->jsonResponse(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        if (in_array($loan['status'], ['distributed'])) return $this->jsonResponse(['status' => false, 'message' => 'Tidak bisa membatalkan'] ,400);
        $this->loanModel->update($id, ['status' => 'cancelled']);
        return $this->jsonResponse(['status' => true, 'message' => 'Dibatalkan']);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoansTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'borrower_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
            ],
            'borrower_identifier' => [ // NIM/NIP/ID Lain
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'borrower_unit' => [ // Prodi/Bagian
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'contact' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'loan_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'return_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [ // requested, approved, borrowed, returned, overdue, cancelled
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'requested',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ticket_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('loan_date');
        $this->forge->addKey('due_date');
        $this->forge->createTable('loans', true);
    }

    public function down()
    {
        $this->forge->dropTable('loans', true);
    }
}

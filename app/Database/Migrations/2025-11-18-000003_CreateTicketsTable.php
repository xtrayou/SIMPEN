<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketsTable extends Migration
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
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => false,
            ],
            'type' => [ // loan-request, restock, issue, other
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'loan-request',
            ],
            'priority' => [ // low, medium, high, urgent
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'medium',
            ],
            'status' => [ // open, in_progress, resolved, closed
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'open',
            ],
            'requester_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
            ],
            'requester_contact' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'related_loan_id' => [
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
        $this->forge->addKey('type');
        $this->forge->addKey('status');
        $this->forge->createTable('tickets', true);
    }

    public function down()
    {
        $this->forge->dropTable('tickets', true);
    }
}

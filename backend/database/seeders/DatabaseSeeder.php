<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = [
            [
                'number' => 'INV-2026-0001',
                'supplier_name' => 'ТОВ «Логістик Плюс»',
                'supplier_tax_id' => '41234567',
                'net_amount' => 25000.00,
                'vat_amount' => 5000.00,
                'gross_amount' => 30000.00,
                'currency' => 'UAH',
                'status' => InvoiceStatus::Pending,
                'issue_date' => '2026-06-01',
                'due_date' => '2026-07-01',
            ],
            [
                'number' => 'INV-2026-0002',
                'supplier_name' => 'ТОВ «Офіс Сервіс»',
                'supplier_tax_id' => '40112233',
                'net_amount' => 8450.00,
                'vat_amount' => 1690.00,
                'gross_amount' => 10140.00,
                'currency' => 'UAH',
                'status' => InvoiceStatus::Approved,
                'issue_date' => '2026-05-15',
                'due_date' => '2026-06-15',
            ],
            [
                'number' => 'INV-2026-0003',
                'supplier_name' => 'Digital Solutions GmbH',
                'supplier_tax_id' => 'DE123456789',
                'net_amount' => 1200.00,
                'vat_amount' => 0.00,
                'gross_amount' => 1200.00,
                'currency' => 'EUR',
                'status' => InvoiceStatus::Rejected,
                'issue_date' => '2026-04-20',
                'due_date' => '2026-05-20',
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::query()->updateOrCreate(
                ['number' => $invoice['number']],
                $invoice,
            );
        }
    }
}

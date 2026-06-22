<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_invoices(): void
    {
        Invoice::factory()->count(3)->create();

        $this->getJson('/api/invoices')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'number', 'gross_amount', 'status']],
                'meta' => ['current_page', 'last_page'],
            ]);
    }

    public function test_it_creates_an_invoice_with_valid_totals(): void
    {
        $payload = [
            'number' => 'INV-TEST-001',
            'supplier_name' => 'Test Supplier',
            'supplier_tax_id' => '12345678',
            'net_amount' => 1000,
            'vat_amount' => 200,
            'gross_amount' => 1200,
            'currency' => 'UAH',
            'status' => 'pending',
            'issue_date' => '2026-06-01',
            'due_date' => '2026-06-30',
        ];

        $this->postJson('/api/invoices', $payload)
            ->assertCreated()
            ->assertJsonPath('data.number', 'INV-TEST-001');

        $this->assertDatabaseHas('invoices', ['number' => 'INV-TEST-001']);
    }

    public function test_it_rejects_incorrect_gross_amount(): void
    {
        $payload = [
            'number' => 'INV-TEST-002',
            'supplier_name' => 'Test Supplier',
            'supplier_tax_id' => '12345678',
            'net_amount' => 1000,
            'vat_amount' => 200,
            'gross_amount' => 1199,
            'currency' => 'UAH',
            'status' => 'pending',
            'issue_date' => '2026-06-01',
            'due_date' => '2026-06-30',
        ];

        $this->postJson('/api/invoices', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('gross_amount');
    }

    public function test_it_does_not_update_non_pending_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::Approved,
        ]);

        $this->putJson("/api/invoices/{$invoice->id}", [
            'net_amount' => 1000,
            'vat_amount' => 200,
            'gross_amount' => 1200,
            'due_date' => '2026-07-01',
        ])
            ->assertConflict()
            ->assertJsonValidationErrors('status');
    }
}

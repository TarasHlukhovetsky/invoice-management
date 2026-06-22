<?php

namespace App\Services;

use App\Exceptions\InvoiceNotEditableException;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $attributes): Invoice
    {
        return DB::transaction(
            fn(): Invoice => Invoice::query()->create($attributes),
        );
    }

    public function update(Invoice $invoice, array $attributes): Invoice
    {
        if (!$invoice->status->isEditable()) {
            throw InvoiceNotEditableException::forStatus($invoice->status->value);
        }

        return DB::transaction(function () use ($invoice, $attributes): Invoice {
            $invoice->update($attributes);

            return $invoice->refresh();
        });
    }
}

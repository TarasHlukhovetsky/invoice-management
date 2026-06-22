<?php

namespace App\Http\Requests;

use App\Enums\InvoiceStatus;
use App\Http\Requests\Concerns\ValidatesInvoiceAmounts;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInvoiceRequest extends FormRequest
{
    use ValidatesInvoiceAmounts;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => strtoupper((string)$this->input('currency', 'UAH')),
            'status'   => $this->input('status', InvoiceStatus::Pending->value),
        ]);
    }

    public function rules(): array
    {
        return [
            'number'          => ['required', 'string', 'max:100', 'unique:invoices,number'],
            'supplier_name'   => ['required', 'string', 'max:255'],
            'supplier_tax_id' => ['required', 'string', 'max:100'],
            'net_amount'      => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'vat_amount'      => ['required', 'numeric', 'gte:0', 'decimal:0,2'],
            'gross_amount'    => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'currency'        => ['required', 'string', 'size:3', Rule::in(['UAH', 'EUR', 'USD'])],
            'status'          => ['required', Rule::enum(InvoiceStatus::class)],
            'issue_date'      => ['required', 'date_format:Y-m-d'],
            'due_date'        => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateAmountsAndDates($validator, (string)$this->input('issue_date'));
    }
}

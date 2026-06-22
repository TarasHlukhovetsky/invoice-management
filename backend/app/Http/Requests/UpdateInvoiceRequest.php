<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesInvoiceAmounts;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateInvoiceRequest extends FormRequest
{
    use ValidatesInvoiceAmounts;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'net_amount'   => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'vat_amount'   => ['required', 'numeric', 'gte:0', 'decimal:0,2'],
            'gross_amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'due_date'     => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $invoice = $this->route('invoice');

        $this->validateAmountsAndDates($validator, $invoice->issue_date->format('Y-m-d'));
    }
}

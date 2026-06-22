<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Validator;

trait ValidatesInvoiceAmounts
{
    protected function validateAmountsAndDates(Validator $validator, string $issueDate): void
    {
        $validator->after(function (Validator $validator) use ($issueDate): void {
            $data = $this->all();

            if (
                isset($data['net_amount'], $data['vat_amount'], $data['gross_amount'])
                && round((float)$data['gross_amount'], 2) !== round(
                    (float)$data['net_amount'] + (float)$data['vat_amount'],
                    2,
                )
            ) {
                $validator->errors()->add(
                    'gross_amount',
                    'Gross amount must equal net amount plus VAT amount.',
                );
            }

            if (
                isset($data['due_date'])
                && strtotime((string)$data['due_date']) < strtotime($issueDate)
            ) {
                $validator->errors()->add(
                    'due_date',
                    'Due date must be on or after issue date.',
                );
            }
        });
    }
}

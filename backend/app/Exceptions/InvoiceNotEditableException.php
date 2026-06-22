<?php

namespace App\Exceptions;

use RuntimeException;

final class InvoiceNotEditableException extends RuntimeException
{
    public static function forStatus(string $status): self
    {
        return new self(sprintf(
            'Invoice cannot be edited because its status is "%s". Only pending invoices are editable.',
            $status,
        ));
    }
}

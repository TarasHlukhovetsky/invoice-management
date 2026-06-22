<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $netAmount = fake()->randomFloat(2, 500, 50_000);
        $vatAmount = round($netAmount * 0.2, 2);
        $issueDate = fake()->dateTimeBetween('-2 months', 'now');

        return [
            'number'          => 'INV-' . fake()->unique()->numerify('#######'),
            'supplier_name'   => fake()->company(),
            'supplier_tax_id' => fake()->numerify('##########'),
            'net_amount'      => $netAmount,
            'vat_amount'      => $vatAmount,
            'gross_amount'    => $netAmount + $vatAmount,
            'currency'        => 'UAH',
            'status'          => fake()->randomElement(InvoiceStatus::cases()),
            'issue_date'      => $issueDate,
            'due_date'        => (clone $issueDate)->modify('+30 days'),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn(): array => ['status' => InvoiceStatus::Pending]);
    }
}

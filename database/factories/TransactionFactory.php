<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'portfolio_id' => Portfolio::factory(),
            'asset_id' => Asset::factory(),
            'type' => fake()->randomElement([TransactionType::Buy, TransactionType::Sell]),
            'quantity' => fake()->randomFloat(4, 0.0001, 100),
            'price' => fake()->randomFloat(2, 1, 10000),
            'price_currency' => fake()->randomElement(Currency::Usd.Currency::Eur),
            'executed_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }
}

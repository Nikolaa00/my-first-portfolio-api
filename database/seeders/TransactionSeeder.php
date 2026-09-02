<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = Asset::query()->get();

        Portfolio::query()->each(function (Portfolio $portfolio) use ($assets): void {
            Transaction::factory()
                ->count(5)
                ->create([
                    'portfolio_id' => $portfolio->id,
                    'asset_id' => $assets->random()->id,
                ]);
        });
    }
}

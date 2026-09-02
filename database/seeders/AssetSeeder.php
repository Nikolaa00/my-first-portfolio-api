<?php

namespace Database\Seeders;

use App\Enums\AssetType;
use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            ['symbol' => 'AAPL', 'exchange' => 'NASDAQ', 'name' => 'Apple Inc.', 'asset_type' => AssetType::Stock],
            ['symbol' => 'MSFT', 'exchange' => 'NASDAQ', 'name' => 'Microsoft Corporation', 'asset_type' => AssetType::Stock],
            ['symbol' => 'GOOGL', 'exchange' => 'NASDAQ', 'name' => 'Alphabet Inc.', 'asset_type' => AssetType::Stock],
            ['symbol' => 'BTC', 'exchange' => 'BINANCE', 'name' => 'Bitcoin', 'asset_type' => AssetType::Crypto],
            ['symbol' => 'AAPL', 'exchange' => 'LSE', 'name' => 'Apple Inc. (London)', 'asset_type' => AssetType::Stock],
        ];

        foreach ($assets as $asset) {
            Asset::factory()->create($asset);
        }
    }
}

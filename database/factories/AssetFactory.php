<?php

namespace Database\Factories;

use App\Enums\AssetType;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assets = [
            ['symbol' => 'AAPL', 'exchange' => 'NASDAQ', 'name' => 'Apple Inc.', 'asset_type' => AssetType::Stock],
            ['symbol' => 'MSFT', 'exchange' => 'NASDAQ', 'name' => 'Microsoft Corporation', 'asset_type' => AssetType::Stock],
            ['symbol' => 'BTC', 'exchange' => 'BINANCE', 'name' => 'Bitcoin', 'asset_type' => AssetType::Crypto],
            ['symbol' => 'GOOGL', 'exchange' => 'NASDAQ', 'name' => 'Alphabet Inc.', 'asset_type' => AssetType::Stock],
            ['symbol' => 'AAPL', 'exchange' => 'LSE', 'name' => 'Apple Inc. (London)', 'asset_type' => AssetType::Stock],
        ];

        $asset = fake()->unique()->randomElement($assets);

        return [
            'symbol' => $asset['symbol'],
            'exchange' => $asset['exchange'],
            'name' => $asset['name'],
            'asset_type' => $asset['asset_type'],
        ];
    }
}

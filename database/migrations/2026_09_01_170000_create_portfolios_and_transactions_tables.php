<?php

use App\Enums\AssetType;
use App\Enums\Currency;
use App\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('currency')->default('EUR');
            $table->timestamps();
        });

        $currencyValues = $this->enumValues(Currency::cases());
        DB::statement("ALTER TABLE portfolios ADD CONSTRAINT portfolios_currency_check CHECK (currency IN ('{$currencyValues}'))");

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('symbol');
            $table->string('exchange');
            $table->string('name')->nullable();
            $table->string('asset_type')->default('stock');
            $table->timestamps();

            $table->unique(['symbol', 'exchange']);
        });

        $assetTypeValues = $this->enumValues(AssetType::cases());
        DB::statement("ALTER TABLE assets ADD CONSTRAINT assets_asset_type_check CHECK (asset_type IN ('{$assetTypeValues}'))");

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->restrictOnDelete();
            $table->string('type');
            $table->decimal('quantity', 15, 4);
            $table->decimal('price', 15, 2);
            $table->string('price_currency');
            $table->dateTime('executed_at');
            $table->timestamps();

            $table->index(['portfolio_id', 'executed_at']);
        });

        $transactionTypeValues = $this->enumValues(TransactionType::cases());
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_type_check CHECK (type IN ('{$transactionTypeValues}'))");
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_price_currency_check CHECK (price_currency IN ('{$currencyValues}'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('portfolios');
    }

    /**
     * @param  array<int, Currency|TransactionType|AssetType>  $cases
     */
    private function enumValues(array $cases): string
    {
        return collect($cases)->map->value->implode("', '");
    }
};

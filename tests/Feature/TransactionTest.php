<?php

namespace Tests\Feature;

use App\Enums\Currency;
use App\Enums\TransactionType;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_transactions_for_own_portfolio(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $asset = Asset::factory()->create();
        $transaction = Transaction::factory()->for($portfolio)->for($asset)->create();
        Transaction::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/portfolios/{$portfolio->id}/transactions")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $transaction->id);
    }

    public function test_authenticated_user_can_create_transaction_for_own_portfolio(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $asset = Asset::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->postJson("/api/portfolios/{$portfolio->id}/transactions", [
                'asset_id' => $asset->id,
                'type' => TransactionType::Buy->value,
                'quantity' => 10.5,
                'price' => 150.25,
                'price_currency' => Currency::Usd->value,
                'executed_at' => '2026-01-15 10:30:00',
            ])
            ->assertCreated()
            ->assertJsonPath('data.portfolio_id', $portfolio->id)
            ->assertJsonPath('data.asset_id', $asset->id)
            ->assertJsonPath('data.type', TransactionType::Buy->value);

        $this->assertDatabaseHas('transactions', [
            'portfolio_id' => $portfolio->id,
            'asset_id' => $asset->id,
        ]);
    }

    public function test_authenticated_user_can_view_own_transaction(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $transaction = Transaction::factory()->for($portfolio)->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/transactions/{$transaction->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id);
    }

    public function test_authenticated_user_can_update_own_transaction(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $asset = Asset::factory()->create();
        $transaction = Transaction::factory()->for($portfolio)->create([
            'quantity' => 1,
            'price' => 100,
        ]);
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->putJson("/api/transactions/{$transaction->id}", [
                'asset_id' => $asset->id,
                'type' => TransactionType::Sell->value,
                'quantity' => 2.5,
                'price' => 200.75,
                'price_currency' => Currency::Eur->value,
                'executed_at' => '2026-02-01 14:00:00',
            ])
            ->assertOk()
            ->assertJsonPath('data.type', TransactionType::Sell->value)
            ->assertJsonPath('data.quantity', '2.5000');
    }

    public function test_authenticated_user_can_delete_own_transaction(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $transaction = Transaction::factory()->for($portfolio)->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->deleteJson("/api/transactions/{$transaction->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_user_cannot_list_transactions_for_another_users_portfolio(): void
    {
        $user = User::factory()->create();
        $otherPortfolio = Portfolio::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/portfolios/{$otherPortfolio->id}/transactions")
            ->assertForbidden();
    }

    public function test_user_cannot_view_another_users_transaction(): void
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/transactions/{$transaction->id}")
            ->assertForbidden();
    }

    public function test_store_transaction_requires_valid_data(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->postJson("/api/portfolios/{$portfolio->id}/transactions", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['asset_id', 'type', 'quantity', 'price', 'price_currency', 'executed_at']);
    }
}

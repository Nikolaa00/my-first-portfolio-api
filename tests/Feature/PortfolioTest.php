<?php

namespace Tests\Feature;

use App\Enums\Currency;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_own_portfolios(): void
    {
        $user = User::factory()->create();
        $ownPortfolio = Portfolio::factory()->for($user)->create(['name' => 'My Portfolio']);
        Portfolio::factory()->create();

        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/portfolios')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ownPortfolio->id)
            ->assertJsonPath('data.0.name', 'My Portfolio');
    }

    public function test_authenticated_user_can_create_portfolio(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/portfolios', [
                'name' => 'Growth Fund',
                'description' => 'Long-term holdings',
                'currency' => Currency::Eur->value,
            ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Growth Fund')
            ->assertJsonPath('data.currency', Currency::Eur->value)
            ->assertJsonPath('data.user_id', $user->id);

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'name' => 'Growth Fund',
        ]);
    }

    public function test_authenticated_user_can_view_own_portfolio(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/portfolios/{$portfolio->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $portfolio->id);
    }

    public function test_authenticated_user_can_update_own_portfolio(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create(['name' => 'Old Name']);
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->putJson("/api/portfolios/{$portfolio->id}", [
                'name' => 'Updated Name',
                'description' => 'Updated description',
                'currency' => Currency::Usd->value,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.currency', Currency::Usd->value);
    }

    public function test_authenticated_user_can_delete_own_portfolio(): void
    {
        $user = User::factory()->create();
        $portfolio = Portfolio::factory()->for($user)->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->deleteJson("/api/portfolios/{$portfolio->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('portfolios', [
            'id' => $portfolio->id,
        ]);
    }

    public function test_user_cannot_view_another_users_portfolio(): void
    {
        $user = User::factory()->create();
        $otherPortfolio = Portfolio::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/portfolios/{$otherPortfolio->id}")
            ->assertForbidden();
    }

    public function test_user_cannot_update_another_users_portfolio(): void
    {
        $user = User::factory()->create();
        $otherPortfolio = Portfolio::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->putJson("/api/portfolios/{$otherPortfolio->id}", [
                'name' => 'Hacked',
                'description' => null,
                'currency' => Currency::Eur->value,
            ])
            ->assertForbidden();
    }

    public function test_store_portfolio_requires_valid_data(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/portfolios', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'currency']);
    }

    public function test_unauthenticated_user_cannot_access_portfolios(): void
    {
        $this->getJson('/api/portfolios')->assertUnauthorized();
    }
}

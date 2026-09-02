<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiErrorTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_requests_return_unified_error_format(): void
    {
        $this->getJson('/api/portfolios')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.'])
            ->assertJsonMissingPath('errors');
    }

    public function test_validation_errors_return_unified_error_format(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/portfolios', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['name', 'currency']]);
    }

    public function test_authorization_errors_return_unified_error_format(): void
    {
        $user = User::factory()->create();
        $otherPortfolio = Portfolio::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson("/api/portfolios/{$otherPortfolio->id}")
            ->assertForbidden()
            ->assertJsonStructure(['message'])
            ->assertJsonMissingPath('errors');
    }

    public function test_model_not_found_returns_unified_error_format(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/portfolios/99999')
            ->assertNotFound()
            ->assertJson(['message' => 'Resource not found.'])
            ->assertJsonMissingPath('errors');
    }

    public function test_admin_forbidden_returns_unified_error_format(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/admin/users')
            ->assertForbidden()
            ->assertJsonStructure(['message'])
            ->assertJsonMissingPath('errors');
    }

    public function test_unknown_api_route_returns_unified_error_format(): void
    {
        $this->getJson('/api/does-not-exist')
            ->assertNotFound()
            ->assertJson(['message' => 'Route not found.'])
            ->assertJsonMissingPath('errors');
    }
}

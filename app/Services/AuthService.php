<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    private const TOKEN_NAME = 'api';

    /**
     * @param  array{name: string, email: string, password: string}  $data
     * @return array{user: User, token: string}
     */
    public function register(array $data): array
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::User,
        ]);

        return [
            'user' => $user,
            'token' => $this->createToken($user),
        ];
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(User $user): array
    {
        return [
            'user' => $user,
            'token' => $this->createToken($user),
        ];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    private function createToken(User $user): string
    {
        return $user->createToken(self::TOKEN_NAME)->plainTextToken;
    }
}

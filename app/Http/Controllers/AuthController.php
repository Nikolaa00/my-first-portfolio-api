<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        $result = $authService->register($request->validated());

        return $this->respondWithToken($result['user'], $result['token'], 201);
    }

    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = Auth::user();

        $result = $authService->login($user);

        return $this->respondWithToken($result['user'], $result['token']);
    }

    public function logout(Request $request, AuthService $authService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $authService->logout($user);

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    private function respondWithToken(User $user, string $token, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => (new UserResource($user))->resolve(),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], $status);
    }
}

<?php

namespace App\Policies;

use App\Models\Portfolio;
use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user, Portfolio $portfolio): bool
    {
        return $user->isAdmin() || $user->id === $portfolio->user_id;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->id === $transaction->portfolio->user_id;
    }

    public function create(User $user, Portfolio $portfolio): bool
    {
        return $user->isAdmin() || $user->id === $portfolio->user_id;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->id === $transaction->portfolio->user_id;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->isAdmin() || $user->id === $transaction->portfolio->user_id;
    }
}

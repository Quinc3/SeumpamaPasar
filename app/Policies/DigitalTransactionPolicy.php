<?php

namespace App\Policies;

use App\Models\DigitalTransaction;
use App\Models\User;

class DigitalTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DigitalTransaction $transaction): bool
    {
        return $user->id === $transaction->user_id
            || $user->role === \App\Enums\UserRole::Admin;
    }

    public function create(User $user): bool
    {
        return true;
    }
}
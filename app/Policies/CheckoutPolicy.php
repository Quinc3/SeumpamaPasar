<?php

namespace App\Policies;

use App\Models\Checkout;
use App\Models\User;

class CheckoutPolicy
{
    public function view(User $user, Checkout $checkout): bool
    {
        return $user->id === $checkout->user_id
            || $user->role === \App\Enums\UserRole::Admin;
    }

    public function pay(User $user, Checkout $checkout): bool
    {
        return $user->id === $checkout->user_id;
    }

    public function cancel(User $user, Checkout $checkout): bool
    {
        return $user->id === $checkout->user_id;
    }
}
<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id
            || $order->sellerProfile->user_id === $user->id
            || $user->role === \App\Enums\UserRole::Admin;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->sellerProfile->user_id
            || $user->role === \App\Enums\UserRole::Admin;
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->id === $order->sellerProfile->user_id;
    }
}
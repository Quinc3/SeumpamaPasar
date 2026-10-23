<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->sellerProfile !== null;
    }

    public function update(User $user, Product $product): bool
    {
        return $product->sellerProfile->user_id === $user->id;
    }

    public function archive(User $user, Product $product): bool
    {
        return $product->sellerProfile->user_id === $user->id;
    }

    public function moderate(User $user): bool
    {
        return $user->role === \App\Enums\UserRole::Admin;
    }
}
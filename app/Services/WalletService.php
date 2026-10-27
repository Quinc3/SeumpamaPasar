<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class WalletService
{
    public const MAX_BALANCE = 10_000_000;

    public function getOrCreate(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
    }

    public function balance(User $user): float
    {
        return (float) ($user->wallet?->balance ?? 0);
    }

    public function credit(User $user, float $amount, string $type, string $description, ?Model $related = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal harus lebih dari 0.');
        }

        return DB::transaction(function () use ($user, $amount, $type, $description, $related) {
            $wallet = Wallet::query()->where('user_id', $user->id)->lockForUpdate()->first();

            if (! $wallet) {
                Wallet::create(['user_id' => $user->id, 'balance' => 0]);
                $wallet = Wallet::query()->where('user_id', $user->id)->lockForUpdate()->first();
            }

            $newBalance = (float) $wallet->balance + $amount;

            if ($type === 'topup' && $newBalance > self::MAX_BALANCE) {
                throw new InvalidArgumentException(
                    'Saldo maksimum Rp ' . number_format(self::MAX_BALANCE, 0, ',', '.') . '. Top up ini akan melebihi batas.'
                );
            }

            $wallet->update(['balance' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'WTX-' . strtoupper(Str::random(10)),
                'description' => $description,
                'related_type' => $related ? $related->getMorphClass() : null,
                'related_id' => $related?->getKey(),
            ]);
        });
    }

    public function debit(User $user, float $amount, string $type, string $description, ?Model $related = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal harus lebih dari 0.');
        }

        return DB::transaction(function () use ($user, $amount, $type, $description, $related) {
            $wallet = Wallet::query()->where('user_id', $user->id)->lockForUpdate()->first();

            if (! $wallet || (float) $wallet->balance < $amount) {
                throw new RuntimeException('Saldo tidak mencukupi.');
            }

            $newBalance = (float) $wallet->balance - $amount;
            $wallet->update(['balance' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'WTX-' . strtoupper(Str::random(10)),
                'description' => $description,
                'related_type' => $related ? $related->getMorphClass() : null,
                'related_id' => $related?->getKey(),
            ]);
        });
    }

    public function history(User $user, int $perPage = 15)
    {
        return WalletTransaction::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
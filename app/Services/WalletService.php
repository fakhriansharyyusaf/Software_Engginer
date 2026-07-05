<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

/**
 * All wallet balance mutations must go through this service so that
 * wallet_transactions always stays in sync with the wallet balance
 * (single source of truth, easy to audit for reports and reversals).
 */
class WalletService
{
    public static function ensureWallet(User $user): \App\Models\Wallet
    {
        return $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
    }

    public static function credit(User $user, float $amount, string $type, ?string $description = null, ?string $referenceType = null, ?int $referenceId = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $type, $description, $referenceType, $referenceId) {
            self::ensureWallet($user);
            $wallet = \App\Models\Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            $wallet->increment('balance', $amount);

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    /**
     * @throws \RuntimeException when balance is insufficient
     */
    public static function debit(User $user, float $amount, string $type, ?string $description = null, ?string $referenceType = null, ?int $referenceId = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $type, $description, $referenceType, $referenceId) {
            self::ensureWallet($user);
            $wallet = \App\Models\Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ((float) $wallet->balance < $amount) {
                throw new \RuntimeException('Saldo wallet tidak mencukupi.');
            }

            $wallet->decrement('balance', $amount);

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => -$amount,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    public static function hasReversal(User $user, string $type, string $referenceType, int $referenceId): bool
    {
        return WalletTransaction::where('user_id', $user->id)
            ->where('type', $type)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->exists();
    }
}

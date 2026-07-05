<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\Voucher;

/**
 * Discount rule (documented in README):
 * - A checkout may use either ONE Voucher OR ONE Promo, never both
 *   (they are not combinable). This keeps the calculation predictable.
 * - Discount is calculated on the subtotal BEFORE PPN 12% is applied,
 *   and PPN is calculated on (subtotal - discount).
 */
class DiscountService
{
    /**
     * @return array{type: 'voucher'|'promo', model: Voucher|Promo, amount: float}
     *
     * @throws \RuntimeException when the code is invalid, expired, or exhausted
     */
    public static function validate(string $code, float $subtotal): array
    {
        $now = TimeService::now();

        $voucher = Voucher::where('code', $code)->first();
        if ($voucher) {
            if ($voucher->isExpired($now)) {
                throw new \RuntimeException('Voucher sudah kedaluwarsa.');
            }
            if (! $voucher->hasRemainingUsage()) {
                throw new \RuntimeException('Voucher sudah habis digunakan.');
            }

            return [
                'type' => 'voucher',
                'model' => $voucher,
                'amount' => self::calculateAmount($voucher->discount_type, (float) $voucher->discount_value, $subtotal),
            ];
        }

        $promo = Promo::where('code', $code)->first();
        if ($promo) {
            if ($promo->isExpired($now)) {
                throw new \RuntimeException('Promo sudah kedaluwarsa.');
            }

            return [
                'type' => 'promo',
                'model' => $promo,
                'amount' => self::calculateAmount($promo->discount_type, (float) $promo->discount_value, $subtotal),
            ];
        }

        throw new \RuntimeException('Kode voucher/promo tidak ditemukan.');
    }

    private static function calculateAmount(string $type, float $value, float $subtotal): float
    {
        $amount = $type === 'percent' ? ($subtotal * $value / 100) : $value;

        return min($amount, $subtotal); // discount can never exceed subtotal
    }
}

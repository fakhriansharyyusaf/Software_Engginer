<?php

namespace Database\Seeders;

use App\Models\Promo;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherPromoSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::firstOrCreate(['code' => 'SEAPEDIA10'], [
            'discount_type' => 'percent',
            'discount_value' => 10,
            'expiry_date' => now()->addMonths(6),
            'usage_limit' => 100,
            'used_count' => 0,
        ]);

        Voucher::firstOrCreate(['code' => 'HEMAT20K'], [
            'discount_type' => 'fixed',
            'discount_value' => 20000,
            'expiry_date' => now()->addMonths(6),
            'usage_limit' => 50,
            'used_count' => 0,
        ]);

        Voucher::firstOrCreate(['code' => 'EXPIRED5'], [
            'discount_type' => 'percent',
            'discount_value' => 5,
            'expiry_date' => now()->subDays(3), // intentionally expired, for demo/testing
            'usage_limit' => 10,
            'used_count' => 0,
        ]);

        Promo::firstOrCreate(['code' => 'PROMOAKHIRTAHUN'], [
            'discount_type' => 'percent',
            'discount_value' => 15,
            'expiry_date' => now()->addMonths(3),
        ]);
    }
}

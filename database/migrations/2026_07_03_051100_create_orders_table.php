<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('delivery_method', ['instant', 'next_day', 'regular']);

            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('delivery_fee', 14, 2);
            $table->decimal('ppn', 14, 2);
            $table->decimal('total', 14, 2);

            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->foreignId('promo_id')->nullable()->constrained('promos')->nullOnDelete();

            // Main lifecycle: Sedang Dikemas, Menunggu Pengirim, Sedang Dikirim, Pesanan Selesai, Dikembalikan
            $table->string('status')->default('Sedang Dikemas');

            $table->dateTime('sla_due_at')->nullable();
            $table->dateTime('overdue_processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

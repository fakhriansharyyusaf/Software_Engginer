<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // topup, payment, refund, seller_income, seller_income_reversal, driver_earning
            $table->string('type');
            $table->decimal('amount', 14, 2); // positive = credit, negative = debit
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable(); // e.g. Order
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

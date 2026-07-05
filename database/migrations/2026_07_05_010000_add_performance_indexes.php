<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the queries that run most often (overdue scan, driver job
 * board, admin monitoring, wallet history) — added as a separate
 * migration (rather than editing the original create_* files) so it's
 * safe to run on a database that's already migrated.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! $this->indexExists('orders', 'orders_status_sla_due_at_index')) {
                $table->index(['status', 'sla_due_at'], 'orders_status_sla_due_at_index');
            }
        });

        Schema::table('delivery_jobs', function (Blueprint $table) {
            if (! $this->indexExists('delivery_jobs', 'delivery_jobs_status_index')) {
                $table->index('status', 'delivery_jobs_status_index');
            }
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (! $this->indexExists('wallet_transactions', 'wallet_transactions_user_id_type_index')) {
                $table->index(['user_id', 'type'], 'wallet_transactions_user_id_type_index');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! $this->indexExists('products', 'products_name_index')) {
                $table->index('name', 'products_name_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $t) => $t->dropIndex('orders_status_sla_due_at_index'));
        Schema::table('delivery_jobs', fn (Blueprint $t) => $t->dropIndex('delivery_jobs_status_index'));
        Schema::table('wallet_transactions', fn (Blueprint $t) => $t->dropIndex('wallet_transactions_user_id_type_index'));
        Schema::table('products', fn (Blueprint $t) => $t->dropIndex('products_name_index'));
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $rows = Schema::getConnection()->select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$indexName]);

            return count($rows) > 0;
        } catch (\Throwable $e) {
            // Non-MySQL connection (e.g. sqlite in local tests) — just let the
            // CREATE INDEX attempt run; Schema::table will no-op gracefully
            // if it already exists on most drivers used for testing.
            return false;
        }
    }
};

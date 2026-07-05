<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Active role is persisted on the user row (not only in the web
     * session) so that the SAME authorization rule works for both the
     * session-based web app and the token-based JSON API.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'active_role')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('active_role')->nullable()->after('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('active_role');
        });
    }
};

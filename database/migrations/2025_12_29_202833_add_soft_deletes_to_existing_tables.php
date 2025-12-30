<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar soft deletes a personal_access_tokens (Sanctum)
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_access_tokens', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Adicionar soft deletes a password_reset_tokens
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Adicionar soft deletes a failed_jobs
        Schema::table('failed_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('failed_jobs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

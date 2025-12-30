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
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Adicionar coluna revoked_at para rastrear quando o token foi revogado
            $table->timestamp('revoked_at')->nullable()->after('expires_at');
            // softDeletes() já foi criado pelo modelo, apenas garantir que existe
            if (!Schema::hasColumn('personal_access_tokens', 'deleted_at')) {
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
            $table->dropColumn('revoked_at');
            $table->dropSoftDeletes();
        });
    }
};

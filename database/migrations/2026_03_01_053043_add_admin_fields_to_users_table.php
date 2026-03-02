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
        Schema::table('users', function (Blueprint $table) {
            // Adicionar colunas se não existirem
            if (!Schema::hasColumn('users', 'profession')) {
                $table->string('profession')->nullable();
            }
            if (!Schema::hasColumn('users', 'license_active')) {
                $table->boolean('license_active')->default(false);
            }
            if (!Schema::hasColumn('users', 'license_expires_at')) {
                $table->dateTime('license_expires_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'admin_notes')) {
                $table->text('admin_notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'profession')) $cols[] = 'profession';
            if (Schema::hasColumn('users', 'license_active')) $cols[] = 'license_active';
            if (Schema::hasColumn('users', 'license_expires_at')) $cols[] = 'license_expires_at';
            if (Schema::hasColumn('users', 'admin_notes')) $cols[] = 'admin_notes';
            
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};

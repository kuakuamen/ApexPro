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
            $table->text('injuries')->nullable()->after('role'); // Lesões
            $table->text('medications')->nullable()->after('injuries'); // Medicamentos
            $table->text('surgeries')->nullable()->after('medications'); // Cirurgias
            $table->string('availability_time')->default('60min')->after('surgeries'); // Tempo (30, 45, 60, 90)
            $table->string('frequency')->default('3x')->after('availability_time'); // Frequência (2x, 3x...)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['injuries', 'medications', 'surgeries', 'availability_time', 'frequency']);
        });
    }
};

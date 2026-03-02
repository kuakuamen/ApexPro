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
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->text('injuries')->nullable()->after('notes'); // Histórico de Lesões
            $table->text('medications')->nullable()->after('injuries'); // Uso de Medicamentos
            $table->text('surgeries')->nullable()->after('medications'); // Cirurgias Prévias
            $table->text('pain_points')->nullable()->after('surgeries'); // Dores/Desconfortos Atuais
            $table->text('habits')->nullable()->after('pain_points'); // Hábitos (Fuma, Bebe, Sono)
            $table->string('goal')->nullable()->after('habits'); // Objetivo Específico desta fase
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->dropColumn(['injuries', 'medications', 'surgeries', 'pain_points', 'habits', 'goal']);
        });
    }
};

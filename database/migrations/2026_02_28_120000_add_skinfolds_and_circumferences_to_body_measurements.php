<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            // Dobras cutâneas (mm)
            $table->decimal('subescapular', 6, 2)->nullable();
            $table->decimal('tricipital', 6, 2)->nullable();
            $table->decimal('bicipital', 6, 2)->nullable();
            $table->decimal('toracica', 6, 2)->nullable();
            $table->decimal('abdominal_fold', 6, 2)->nullable();
            $table->decimal('axilar_media', 6, 2)->nullable();
            $table->decimal('suprailiaca', 6, 2)->nullable();
            $table->decimal('coxa_fold', 6, 2)->nullable();
            $table->decimal('panturrilha_fold', 6, 2)->nullable();
            $table->decimal('sum_skinfolds', 8, 2)->nullable();

            // Circunferências adicionais (cm)
            $table->decimal('ombro', 6, 2)->nullable();
            $table->decimal('torax', 6, 2)->nullable();
            $table->decimal('abdomen_inferior', 6, 2)->nullable();
            $table->decimal('left_arm_contracted', 6, 2)->nullable();
            $table->decimal('right_arm_contracted', 6, 2)->nullable();
            $table->decimal('left_forearm', 6, 2)->nullable();
            $table->decimal('right_forearm', 6, 2)->nullable();

            $table->decimal('left_thigh_proximal', 6, 2)->nullable();
            $table->decimal('left_thigh_medial', 6, 2)->nullable();
            $table->decimal('left_thigh_distal', 6, 2)->nullable();
            $table->decimal('right_thigh_proximal', 6, 2)->nullable();
            $table->decimal('right_thigh_medial', 6, 2)->nullable();
            $table->decimal('right_thigh_distal', 6, 2)->nullable();

            // Métodos e resultados (Guedes, Pollock 3 e 7)
            $table->decimal('guedes_density', 8, 4)->nullable();
            $table->decimal('guedes_fat_pct', 5, 2)->nullable();
            $table->decimal('guedes_fat_mass', 8, 2)->nullable();
            $table->decimal('guedes_lean_mass', 8, 2)->nullable();

            $table->decimal('pollock3_density', 8, 4)->nullable();
            $table->decimal('pollock3_fat_pct', 5, 2)->nullable();
            $table->decimal('pollock3_fat_mass', 8, 2)->nullable();
            $table->decimal('pollock3_lean_mass', 8, 2)->nullable();

            $table->decimal('pollock7_density', 8, 4)->nullable();
            $table->decimal('pollock7_fat_pct', 5, 2)->nullable();
            $table->decimal('pollock7_fat_mass', 8, 2)->nullable();
            $table->decimal('pollock7_lean_mass', 8, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->dropColumn([
                'subescapular','tricipital','bicipital','toracica','abdominal_fold','axilar_media','suprailiaca','coxa_fold','panturrilha_fold','sum_skinfolds',
                'ombro','torax','abdomen_inferior','left_arm_contracted','right_arm_contracted','left_forearm','right_forearm',
                'left_thigh_proximal','left_thigh_medial','left_thigh_distal','right_thigh_proximal','right_thigh_medial','right_thigh_distal',
                'guedes_density','guedes_fat_pct','guedes_fat_mass','guedes_lean_mass',
                'pollock3_density','pollock3_fat_pct','pollock3_fat_mass','pollock3_lean_mass',
                'pollock7_density','pollock7_fat_pct','pollock7_fat_mass','pollock7_lean_mass'
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->string('selected_protocol', 20)->nullable()->after('sum_skinfolds')
                ->comment('Protocolo escolhido pelo profissional: guedes, pollock3 ou pollock7');
        });
    }

    public function down(): void
    {
        Schema::table('body_measurements', function (Blueprint $table) {
            $table->dropColumn('selected_protocol');
        });
    }
};

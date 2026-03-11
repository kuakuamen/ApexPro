<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('address_cep', 9)->nullable()->after('address');
            $table->string('address_state', 2)->nullable()->after('address_cep');
            $table->string('address_city')->nullable()->after('address_state');
            $table->string('address_street')->nullable()->after('address_city');
            $table->string('address_neighborhood')->nullable()->after('address_street');
            $table->string('address_number', 30)->nullable()->after('address_neighborhood');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address_cep',
                'address_state',
                'address_city',
                'address_street',
                'address_neighborhood',
                'address_number',
            ]);
        });
    }
};


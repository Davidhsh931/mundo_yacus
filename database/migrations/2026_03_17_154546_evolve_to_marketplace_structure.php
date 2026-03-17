<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guinea_pigs', function (Blueprint $table) {
            // Verificamos y agregamos cada columna si no existe
            if (!Schema::hasColumn('guinea_pigs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('guinea_pigs', 'species')) {
                $table->string('species')->nullable()->after('name');
            }
            if (!Schema::hasColumn('guinea_pigs', 'product_state')) {
                $table->string('product_state')->nullable()->after('price');
            }
            if (!Schema::hasColumn('guinea_pigs', 'specifications')) {
                $table->json('specifications')->nullable()->after('product_state');
            }
            if (!Schema::hasColumn('guinea_pigs', 'ia_verification')) {
                $table->json('ia_verification')->nullable()->after('specifications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guinea_pigs', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'species', 'product_state', 'specifications', 'ia_verification']);
        });
    }
};
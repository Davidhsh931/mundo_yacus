<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guinea_pig_images', function (Blueprint $table) {

            $table->id();
        $table->foreignId('guinea_pig_id')->constrained()->onDelete('cascade');
        $table->string('image_path');
        $table->integer('position')->default(0); // <--- SIN LA PALABRA "AFTER"
        $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::table('guinea_pig_images', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
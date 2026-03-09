<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guinea_pigs', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // nombre -> name
            $table->string('breed');             // raza -> breed
            $table->decimal('average_weight', 5, 2); // peso_promedio -> average_weight
            $table->decimal('price', 8, 2);     // precio -> price
            $table->integer('stock')->default(0);
            $table->text('description')->nullable(); // descripcion -> description
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('cascade');       // categoria_id -> category_id
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guinea_pigs');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onDelete('cascade');
            $table->foreignId('guinea_pig_id')
                  ->constrained('guinea_pigs')
                  ->restrictOnDelete(); // cuy_id -> guinea_pig_id
            $table->integer('quantity');             // cantidad -> quantity
            $table->decimal('unit_price', 8, 2);     // precio_unitario -> unit_price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
<?php

namespace Database\Seeders;

// database/seeders/MundoYacusAnalyzerSeeder.php
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\GuineaPig;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MundoYacusAnalyzerSeeder extends Seeder
{
    public function run()
    {
        $user = User::first() ?? User::factory()->create();
        $cuyes = GuineaPig::all();

        for ($i = 6; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            
            // Simulamos 10 órdenes por mes
            for ($j = 0; $j < 10; $j++) {
                $fechaAleatoria = $mes->copy()->addDays(rand(1, 28));
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'total' => 0, 
                    'status' => 'paid',
                    'shipping_address' => 'Dirección de prueba',
                    'payment_method' => 'yape',
                    'created_at' => $fechaAleatoria,
                ]);

                $cuyAleatorio = $cuyes->random();
                $cantidad = rand(1, 5);

                OrderItem::create([
                    'order_id' => $order->id,
                    'guinea_pig_id' => $cuyAleatorio->id,
                    'quantity' => $cantidad,
                    'unit_price' => $cuyAleatorio->price,
                    'created_at' => $fechaAleatoria,
                ]);

                $order->update(['total' => $cuyAleatorio->price * $cantidad]);
            }
        }
    }
}
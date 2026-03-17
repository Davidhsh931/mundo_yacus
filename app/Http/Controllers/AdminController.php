<?php

namespace App\Http\Controllers;

use App\Models\GuineaPig;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Total de Cuyes del habitante
        $totalPigs = GuineaPig::where('user_id', $user->id)->count();

        // 2. Total de Pedidos recibidos por este habitante
        $totalOrders = Order::whereHas('items.guineaPig', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        // 3. Clientes únicos que le han comprado
        $totalClients = User::whereHas('orders.items.guineaPig', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();

        // 4. Suma total de ventas (Soles)
        $sales = OrderItem::whereHas('guineaPig', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->sum(DB::raw('price * quantity'));

        return Inertia::render('Admin/Dashboard', [
            'totalPigs'   => $totalPigs,
            'totalOrders' => $totalOrders,
            'totalClients' => $totalClients,
            'sales'       => number_format($sales, 2, '.', ''),
        ]);
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order; // Importante
use Inertia\Inertia; // Importante
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index()
    {
        // Traemos las órdenes con el usuario y los productos (items)
        $orders = Order::with(['user', 'items.guineaPig'])->get();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders
        ]);
    }
}
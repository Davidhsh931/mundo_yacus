<?php

namespace App\Http\Controllers;

use App\Models\GuineaPig;
use App\Models\Order;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPigs = GuineaPig::count();

        $totalOrders = Order::count();

        $totalClients = User::where('role','cliente')->count();

        $sales = Order::sum('total');

        return Inertia::render('Admin/Dashboard',[
            'totalPigs' => $totalPigs,
            'totalOrders' => $totalOrders,
            'totalClients' => $totalClients,
            'sales' => $sales
        ]);
    }
}

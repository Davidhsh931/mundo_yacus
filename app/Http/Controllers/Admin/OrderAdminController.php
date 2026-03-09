<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index()
{
    $orders = Order::with('user','items')->get();

    return Inertia::render('Admin/Orders/Index',[
        'orders'=>$orders
    ]);
}
}

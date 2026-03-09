<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuineaPig;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\OrderItem;

class CartController extends Controller
{
    public function add($id)
    {
    $guineaPig = GuineaPig::findOrFail($id);

    if($guineaPig->stock <= 0){
        return back()->with('error','Producto sin stock');
    }

    $cart = session()->get('cart', []);

    if(isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            "name" => $guineaPig->name,
            "price" => $guineaPig->price,
            "quantity" => 1
        ];
    }

    session()->put('cart', $cart);

    return back();
    }

    public function remove($id)
{
    $cart = session()->get('cart', []);

    if(isset($cart[$id])) {
        unset($cart[$id]);
        session()->put('cart', $cart);
    }

    session()->put('cart', $cart);

    return redirect()->back();
}

public function view()
{
    $cart = session()->get('cart', []);

    return Inertia::render('Cart', [
        'cart' => session()->get('cart', [])
    ]);
}

public function checkout()
{
    $cart = session()->get('cart', []);

    if(empty($cart)){
        return redirect()->back();
    }


    $total = 0;

    foreach($cart as $item){
        $total += $item['price'] * $item['quantity'];
    }

    $order = Order::create([
        'user_id' => auth()->id(),
        'total' => $total,
        'status' => 'pending',
        'shipping_address' => 'direccion temporal',
        'payment_method' => 'cash'
    ]);

    foreach($cart as $id => $item){
        OrderItem::create([
            'order_id' => $order->id,
            'guinea_pig_id' => $id,
            'quantity' => $item['quantity'],
            'unit_price' => $item['price']
        ]);
    }

    session()->forget('cart');

    return redirect('/')->with('success', 'Order placed!');
    
}
}

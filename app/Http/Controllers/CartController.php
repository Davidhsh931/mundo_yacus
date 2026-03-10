<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuineaPig;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function add($id)
{
    $pig = GuineaPig::with('images')->findOrFail($id);

    $cart = session()->get('cart', []);

    $cart[$id] = [
        "name" => $pig->name,
        "price" => $pig->price,
        "quantity" => $cart[$id]['quantity'] ?? 1,
        "image" => $pig->images->first()?->image_path ?? null
    ];

    session()->put('cart', $cart);

    return redirect()->back()->with('success', 'Cuy agregado al carrito');
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

    DB::beginTransaction();

    try {

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

            $pig = GuineaPig::find($id);

            $pig->stock -= $item['quantity'];

            if($pig->stock < 0){
                throw new \Exception("Stock insuficiente");
            }

            $pig->save();
        }

        DB::commit();

        session()->forget('cart');

        return redirect('/')->with('success','Pedido creado');

    } catch(\Exception $e){

        DB::rollBack();

        return back()->with('error','Error al procesar pedido');
    }
}
public function orders()
{
    $orders = Order::with('items.guineaPig')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    return Inertia::render('Orders', [
        'orders' => $orders
    ]);
}

}

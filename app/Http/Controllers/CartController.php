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
        }
        session()->put('cart', $cart);
        return redirect()->back();
    }

    public function view()
    {
        return Inertia::render('Cart', [
            'cart' => session()->get('cart', [])
        ]);
    }

    // --- NUEVO: Función para ver la página de Checkout (Paso 1) ---
    public function viewCheckout()
    {
        $cart = session()->get('cart', []);
        
        if(empty($cart)) {
            return redirect('/cart')->with('error', 'Tu carrito está vacío');
        }

        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return Inertia::render('Checkout', [
            'cart' => $cart,
            'total' => $total
        ]);
    }

    // --- MODIFICADO: Ahora recibe Request para datos reales (Paso 1 cont.) ---
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);

        if(empty($cart)){
            return redirect()->back();
        }

        // Validamos que vengan la dirección y el método de pago
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'payment_method' => 'required|in:yape,plin,transfer,cash',
        ]);

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
                'shipping_address' => $request->shipping_address, // Dato del form
                'payment_method' => $request->payment_method,     // Dato del form
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
                    throw new \Exception("Stock insuficiente para el cuy: " . $pig->name);
                }

                $pig->save();
            }

            DB::commit();
            session()->forget('cart');

            // --- PASO 3: Redirigir a la página de éxito ---
            return redirect()->route('order.success', $order->id)->with('success','Pedido creado');

        } catch(\Exception $e){
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function orders()
    {
        $orders = Order::with('items.guineaPig.images')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Orders', [
            'orders' => $orders
        ]);
    }
}
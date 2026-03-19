<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuineaPigController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VisionController;
use App\Http\Controllers\VisionComentController;
use App\Http\Controllers\Admin\GuineaPigAdminController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\GuineaPig;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/*
|--------------------------------------------------------------------------
| MUNDO YACUS AI - RUTAS DEL SISTEMA UNIFICADO 2026
|--------------------------------------------------------------------------
*/

// --- 1. INTELIGENCIA ARTIFICIAL (IA) ---
Route::post('/vision/analyze', [VisionController::class, 'analyze']);
Route::post('/vision/analyze/coment', [VisionComentController::class, 'analyze']);
Route::get('/vision/analyze/coment', function () {
    return redirect('/admin/CreatePigComent'); 
});

// Predicción de Stock con Scikit-learn
Route::get('/api/cuy/sugerir-stock/{id}', [GuineaPigController::class, 'sugerirStock']);

// --- 2. DASHBOARD UNIFICADO ---
Route::get('/dashboard', function () {
    return Inertia::render('Admin/Dashboard', [
        'totalPigs' => (int) GuineaPig::count(),
        'totalOrders' => (int) Order::count(),
        'totalClients' => (int) User::where('id', '!=', auth()->id())->count(),
        'sales' => (float) Order::sum('total'),
    ]);
})->middleware(['auth'])->name('dashboard');

Route::get('/admin/dashboard', function () {
    return redirect('/dashboard');
});

// --- 3. MERCADO DIRECTO (GESTIÓN) ---
Route::prefix('admin')->middleware('auth')->group(function(){
    Route::get('/guinea-pigs', [GuineaPigAdminController::class, 'index'])->name('guinea-pigs.index');
    Route::get('/guinea-pigs/create', [GuineaPigAdminController::class, 'create'])->name('guinea-pigs.create');
    
    // RUTA VITAL: Procesa imagen + stock + especificaciones
    Route::post('/guinea-pigs', [GuineaPigAdminController::class, 'store'])->name('guinea-pigs.store');
    
    Route::get('/guinea-pigs/{id}/edit', [GuineaPigAdminController::class, 'edit']);
    Route::put('/guinea-pigs/{id}', [GuineaPigAdminController::class, 'update']);
    Route::delete('/guinea-pigs/{id}', [GuineaPigAdminController::class, 'destroy']);

    Route::get('/vision', [VisionController::class, 'index'])->name('admin.vision');

    Route::get('/analytics', function () {
    return Inertia::render('Admin/Analytics');
})->name('admin.analytics');
    });

// --- 4. TIENDA Y EXPERIENCIA DE USUARIO ---
Route::get('/', [GuineaPigController::class, 'index'])->name('home');

// Detalle del producto (Corregido para enviar 'pig' a Vue)
Route::get('/product/{id}', function($id){
    $pig = GuineaPig::with(['images', 'seller'])->findOrFail($id);
    return Inertia::render('Product', [
        'pig' => $pig 
    ]);
});

// Carrito y Checkout
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove']);
Route::get('/cart', [CartController::class, 'view']);
Route::get('/checkout', [CartController::class, 'viewCheckout'])->middleware('auth')->name('checkout');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->middleware('auth')->name('checkout.process');
Route::get('/orders', [CartController::class, 'orders'])->middleware('auth')->name('orders');
Route::get('/order-success/{id}', function($id) {
    $order = Order::findOrFail($id);
    return Inertia::render('OrderSuccess', ['order' => $order]);
})->name('order.success');

// --- 5. AUTENTICACIÓN ---
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard'); 
    }
    return back()->withErrors(['email' => 'Credenciales incorrectas.']);
});

// --- 6. MANTENIMIENTO TÉCNICO ---
Route::get('/fix-db', function () {
    Schema::table('guinea_pigs', function (Blueprint $table) {
        if (!Schema::hasColumn('guinea_pigs', 'user_id')) {
            $table->foreignId('user_id')->nullable()->constrained('users');
        }
        if (!Schema::hasColumn('guinea_pigs', 'species')) {
            $table->string('species')->nullable();
        }
        // NUEVO: Asegura que el stock exista en la base de datos
        if (!Schema::hasColumn('guinea_pigs', 'stock')) {
            $table->integer('stock')->default(1);
        }
        if (!Schema::hasColumn('guinea_pigs', 'product_state')) {
            $table->string('product_state')->nullable();
        }
        if (!Schema::hasColumn('guinea_pigs', 'specifications')) {
            $table->json('specifications')->nullable();
        }
        if (!Schema::hasColumn('guinea_pigs', 'ia_verification')) {
            $table->json('ia_verification')->nullable();
        }
    });
    return "🚀 Mundo Yacus: Base de datos sincronizada con Stock y Usuario.";
});
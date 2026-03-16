<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuineaPigController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VisionController;
use App\Http\Controllers\VisionComentController;
use App\Http\Controllers\Admin\GuineaPigAdminController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::post('/vision/analyze', [VisionController::class, 'analyze']);
Route::get('/vision/analyze', function () {
    return redirect('/admin/CreatePig'); 
});

Route::post('/vision/analyze/coment', [VisionComentController::class, 'analyze']);
Route::get('/vision/analyze/coment', function () {
    return redirect('/admin/CreatePigComent'); 
});

Route::get('/api/cuy/sugerir-stock/{id}', [GuineaPigController::class, 'sugerirStock']);
Route::prefix('admin')->group(function(){

Route::get('/guinea-pigs',[GuineaPigAdminController::class,'index']);
Route::get('/guinea-pigs/create',[GuineaPigAdminController::class,'create']);
Route::get('/guinea-pigs/create/coment',[GuineaPigAdminController::class,'create_coment']);
Route::post('/guinea-pigs',[GuineaPigAdminController::class,'store']);
Route::get('/guinea-pigs/{id}/edit',[GuineaPigAdminController::class,'edit']);
Route::put('/guinea-pigs/{id}',[GuineaPigAdminController::class,'update']);
Route::delete('/guinea-pigs/{id}',[GuineaPigAdminController::class,'destroy']);

});
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
});
Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
});

Route::get('/admin',[AdminController::class,'dashboard']);

Route::post('/admin/guinea-pigs/{id}/upload-image', [GuineaPigController::class, 'uploadImage']);

Route::get('/', [GuineaPigController::class, 'index'])->name('home');

Route::post('/cart/add/{id}', [CartController::class,'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class,'remove']);
Route::get('/cart', [CartController::class,'view']);
Route::post('/cart/checkout', [CartController::class,'checkout'])->middleware('auth');
// En web.php cambia esto:
Route::get('/orders', [CartController::class, 'orders'])->middleware('auth')->name('orders');
// ---------------------------------------------------------------------------^^^^^^^^^^^^^^
Route::get('/product/{id}', function($id){

    $pig = \App\Models\GuineaPig::with('images')->findOrFail($id);

    return Inertia::render('Product',[
        'pig'=>$pig
    ]);

});
Route::get('/login', function () {
    return Inertia::render('Auth/Login'); // O la vista que tengas para loguearte
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/orders'); // Te manda a orders si todo está bien
    }

    return back()->withErrors([
        'email' => 'Las credenciales no coinciden con nuestros registros.',
    ]);
});

Route::get('/checkout', [CartController::class, 'viewCheckout'])->middleware('auth')->name('checkout');
Route::get('/order-success/{id}', function($id) {
    $order = \App\Models\Order::findOrFail($id);
    return Inertia::render('OrderSuccess', ['order' => $order]);
})->name('order.success');
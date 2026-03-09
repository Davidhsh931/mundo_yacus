<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuineaPigController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\GuineaPigAdminController;

Route::prefix('admin')->group(function(){

Route::get('/guinea-pigs',[GuineaPigAdminController::class,'index']);
Route::get('/guinea-pigs/create',[GuineaPigAdminController::class,'create']);
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

Route::get('/', [GuineaPigController::class, 'index'])->name('home');

Route::post('/cart/add/{id}', [CartController::class,'add']);
Route::post('/cart/remove/{id}', [CartController::class,'remove']);
Route::get('/cart', [CartController::class,'view']);
Route::post('/cart/checkout', [CartController::class,'checkout']);

Route::get('/product/{id}', function($id){

    $pig = \App\Models\GuineaPig::with('images')->findOrFail($id);

    return Inertia::render('Product',[
        'pig'=>$pig
    ]);

});
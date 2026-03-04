<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuineaPigController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [GuineaPigController::class, 'index'])->name('home');

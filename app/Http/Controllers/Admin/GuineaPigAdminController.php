<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuineaPigAdminController extends Controller
{
    public function index()
{
    $pigs = GuineaPig::all();

    return Inertia::render('Admin/GuineaPigs/Index',[
        'pigs'=>$pigs
    ]);
}
}

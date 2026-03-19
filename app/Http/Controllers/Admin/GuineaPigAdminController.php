<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Models\GuineaPig;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GuineaPigAdminController extends Controller
{
    public function index()
    {
        $pigs = GuineaPig::with('seller')->get(); // Incluimos al vendedor para saber quién publica

        return Inertia::render('Admin/GuineaPigs/Index', [
            'pigs' => $pigs
        ]);
    }

    public function create()
    {
        // Apuntamos al nuevo archivo que creamos
        return Inertia::render('Admin/CreateProduct'); 
    }

    public function store(Request $request) 
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'stock' => 'required|integer|min:0', // Validamos el nuevo stock
        'species' => 'required',
        'product_state' => 'required',
        'image' => 'required|image|max:2048', 
    ]);

    try {
        // 1. Creamos el animal
        $pig = \App\Models\GuineaPig::create([
            'user_id'         => auth()->id(), 
            'name'            => $request->name,
            'species'         => $request->species,
            'price'           => $request->price,
            'product_state'   => $request->product_state,
            'stock'           => $request->stock, 
            'active'          => true,
            // Importante: Si Vue envía un objeto, Laravel lo convierte a JSON automáticamente si el modelo tiene cast
            'specifications'  => $request->specifications, 
            'ia_verification' => $request->ia_verification, 
        ]);

        // 2. Guardamos la imagen
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');

            \App\Models\GuineaPigImage::create([
                'guinea_pig_id' => $pig->id,
                'image_path'    => $path,
                'position'      => 1
            ]);
        }

        return redirect()->route('home')->with('message', '¡Publicado con éxito!');

    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    public function edit($id)
    {
        $pig = GuineaPig::findOrFail($id);
        return Inertia::render('Admin/EditPig', ['pig' => $pig]);
    }

    public function create_coment()
    {
        return Inertia::render('Admin/CreatePigComent'); 
    }
}
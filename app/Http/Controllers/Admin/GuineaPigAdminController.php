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
        'species' => 'required',
        'product_state' => 'required',
        'stock' => 'nullable|integer' // Validamos el stock
    ]);

    GuineaPig::create([
        'user_id' => auth()->id(), 
        'name' => $request->name,
        'species' => $request->species,
        'price' => $request->price,
        'product_state' => $request->product_state,
        'stock' => $request->stock ?? 1, // Si no viene nada, le ponemos 1 por defecto
        'active' => true, // Aseguramos que esté activo
        'specifications' => $request->custom_attributes,
        'ia_verification' => $request->ia_verification,
    ]);

    return redirect('/admin/guinea-pigs')->with('message', '¡Producto publicado con éxito!');
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
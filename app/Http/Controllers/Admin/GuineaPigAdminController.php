<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuineaPig; // <--- ¡ESTA LÍNEA ES VITAL!
use Inertia\Inertia;       // <--- ESTA TAMBIÉN
use Illuminate\Http\Request;

class GuineaPigAdminController extends Controller
{
    public function index()
    {
        $pigs = GuineaPig::all();

        return Inertia::render('Admin/GuineaPigs/Index', [
            'pigs' => $pigs
        ]);
    }

    public function edit($id)
    {
        // Ahora sí encontrará el modelo para buscar al cuy
        $pig = GuineaPig::findOrFail($id);

        return Inertia::render('Admin/EditPig', [
            'pig' => $pig
        ]);
    }

    public function create()
    {
    // Esto es lo que le dice a Laravel que muestre el formulario de creación
    return inertia('Admin/CreatePig'); 
    // Nota: Asegúrate de que la ruta 'Admin/GuineaPigs/Create' coincida 
    // con la ubicación de tu archivo CreatePig.vue o similar.
    }
    public function create_coment()
    {
    // Esto es lo que le dice a Laravel que muestre el formulario de creación
    return inertia('Admin/CreatePigComent'); 
    // Nota: Asegúrate de que la ruta 'Admin/GuineaPigs/Create' coincida 
    // con la ubicación de tu archivo CreatePig.vue o similar.
    }

}
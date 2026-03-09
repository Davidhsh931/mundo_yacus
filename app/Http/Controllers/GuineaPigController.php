<?php

namespace App\Http\Controllers;

use App\Models\GuineaPig;
use Inertia\Inertia;

class GuineaPigController extends Controller
{
    public function index()
    {
        // Filtra los conejillos activos
        $guineaPigs = GuineaPig::with('category', 'images')
        ->where('active', true)
        ->get();

        // Retorna la vista Inertia en inglés
        return Inertia::render('Home', [
            'guineaPigs' => $guineaPigs
        ]);
    }

    public function show($id)
{
    $guineaPig = GuineaPig::with('images')->findOrFail($id);

    return view('guinea_pigs.show', compact('guineaPig'));
}
}
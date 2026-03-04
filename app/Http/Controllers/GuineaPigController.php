<?php

namespace App\Http\Controllers;

use App\Models\GuineaPig;
use Inertia\Inertia;

class GuineaPigController extends Controller
{
    public function index()
    {
        // Filtra los conejillos activos
        $guineaPigs = GuineaPig::where('active', true)->get();

        // Retorna la vista Inertia en inglés
        return Inertia::render('Home', [
            'guineaPigs' => $guineaPigs
        ]);
    }
}
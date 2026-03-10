<?php

namespace App\Http\Controllers;

use App\Models\GuineaPig;
use App\Models\GuineaPigImage;
use Illuminate\Http\Request;
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
    public function uploadImage(Request $request, $id)
{
    $request->validate([
        'image' => 'required|image|max:2048'
    ]);

    $pig = GuineaPig::findOrFail($id);

    $path = $request->file('image')->store('images', 'public');

    GuineaPigImage::create([
        'guinea_pig_id' => $pig->id,
        'image_path' => '/storage/' . $path,
        'position' => $pig->images()->count() + 1
    ]);

    return back()->with('success', 'Imagen subida correctamente');
}
}
<?php

namespace App\Http\Controllers;

use App\Models\GuineaPig;
use App\Models\GuineaPigImage;
use App\Models\OrderItem; // Añadimos el modelo para mejor legibilidad
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class GuineaPigController extends Controller
{
    public function index()
{
    // Traemos todo lo necesario para el Mercado Directo
    $guineaPigs = \App\Models\GuineaPig::with(['seller', 'images'])->get();

    return Inertia::render('Home', [
        'guineaPigs' => $guineaPigs // Nombre exacto que espera tu Home.vue
    ]);
}

    public function show($id)
{
    // Buscamos el animal con sus imágenes y vendedor
    $pig = GuineaPig::with(['images', 'seller'])->findOrFail($id);
    
    // IMPORTANTE: El nombre aquí debe ser 'pig' para que coincida con web.php y Vue
    return Inertia::render('Product', [
        'pig' => $pig 
    ]);
}

    public function uploadImage(Request $request, $id)
{
    $request->validate(['image' => 'required|image|max:2048']);
    $pig = GuineaPig::findOrFail($id);
    
    // Esto guarda en storage/app/public/images y devuelve "images/archivo.jpg"
    $path = $request->file('image')->store('images', 'public');

    GuineaPigImage::create([
        'guinea_pig_id' => $pig->id,
        // Guardamos solo el path relativo
        'image_path' => $path, 
        'position' => $pig->images()->count() + 1
    ]);

    return back()->with('success', 'Imagen subida correctamente');
}

    /**
     * Esta es la función "Cerebro" que conecta con Python
     */
    public function sugerirStock($id) 
{
    $ventas = OrderItem::where('guinea_pig_id', $id)
                ->select('quantity', 'created_at')
                ->get();

    // --- TRUCO PARA QUE NO SIGA IGUAL: DATOS DE PRUEBA SI ESTÁ VACÍO ---
    if ($ventas->isEmpty()) {
        $ventas = collect([
            ['quantity' => 10, 'created_at' => '2026-01-01'],
            ['quantity' => 15, 'created_at' => '2026-02-01'],
            ['quantity' => 25, 'created_at' => '2026-03-01'],
        ]);
    }

    $jsonVentas = escapeshellarg(json_encode($ventas));
    $scriptPath = base_path('scripts/predict_stock.py');
    
    // Ejecutamos y limpiamos cualquier espacio en blanco
    $prediccionRaw = trim(shell_exec("python3 $scriptPath $jsonVentas"));
    $datosIA = json_decode($prediccionRaw, true);

    return response()->json($datosIA);
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
}
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
    // Añadimos 'seller' para que la página de detalle sepa quién lo vende
    $guineaPig = GuineaPig::with(['images', 'seller'])->findOrFail($id);
    
    // Si estás usando Inertia (como en tu Home.vue), usa esto:
    return Inertia::render('Product', [
        'guineaPig' => $guineaPig
    ]);
}

    public function uploadImage(Request $request, $id)
    {
        $request->validate(['image' => 'required|image|max:2048']);
        $pig = GuineaPig::findOrFail($id);
        $path = $request->file('image')->store('images', 'public');

        GuineaPigImage::create([
            'guinea_pig_id' => $pig->id,
            'image_path' => '/storage/' . $path,
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

        if ($ventas->isEmpty()) {
            return response()->json([
                'stock_sugerido' => 0,
                'metodo' => 'Sin datos',
                'registros' => 0
            ]);
        }

        $jsonVentas = escapeshellarg(json_encode($ventas));
        $scriptPath = base_path('scripts/predict_stock.py');
        $prediccionRaw = shell_exec("python3 $scriptPath $jsonVentas");

        // --- EL CAMBIO VITAL AQUÍ ---
        // Convertimos el texto de Python en un array de PHP
        $datosIA = json_decode($prediccionRaw, true);

        // Si Python falla o no devuelve JSON válido, damos valores por defecto
        if (!$datosIA) {
            return response()->json([
                'stock_sugerido' => 'Error en script',
                'metodo' => 'Error',
                'registros' => 0
            ]);
        }

        // Enviamos el objeto LIMPIO a Vue
        return response()->json($datosIA);
    }

    public function store(Request $request) {
    $product = new GuineaPig();
    $product->user_id = auth()->id(); // El habitante logueado es el dueño
    $product->species = $request->species;
    $product->status_type = $request->status_type;
    $product->specifications = $request->specifications; // Guardamos el JSON
    $product->save();
}
}
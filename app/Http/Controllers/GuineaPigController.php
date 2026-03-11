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
        $guineaPigs = GuineaPig::with('category', 'images')
            ->where('active', true)
            ->get();

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
        // 1. Obtenemos los datos reales (quantity y created_at)
        // Le pasamos todo el historial al Panda para que él decida cómo agrupar
        $ventas = OrderItem::where('guinea_pig_id', $id)
                    ->select('quantity', 'created_at')
                    ->get();

        if ($ventas->isEmpty()) {
            return response()->json([
                'id' => $id,
                'stock_sugerido' => 'Datos insuficientes para predecir',
                'motor_ia' => 'NumPy + Pandas'
            ]);
        }

        // 2. Preparamos el JSON para Python
        $jsonVentas = escapeshellarg(json_encode($ventas));
        
        // 3. Ejecutamos el script de Python
        // base_path nos asegura encontrar la carpeta /scripts en la raíz de tu proyecto
        $scriptPath = base_path('scripts/predict_stock.py');
        $prediccion = shell_exec("python3 $scriptPath $jsonVentas");

        // 4. Respuesta al Frontend
        return response()->json([
            'id' => $id,
            'stock_sugerido' => trim($prediccion),
            'total_registros_analizados' => $ventas->count(),
            'motor_ia' => 'NumPy + Pandas'
        ]);
    }
}
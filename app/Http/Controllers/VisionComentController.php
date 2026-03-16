<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VisionComentController extends Controller
{
    public function analyze(Request $request)
    {
        try {
            // Validamos imagen y ahora también el comentario (opcional)
            $request->validate([
                'image' => 'required|image|max:2048',
                'comentario' => 'nullable|string|max:500'
            ]);

            // 1. Guardar la imagen temporal
            $path = $request->file('image')->store('temp_vision', 'local');
            $fullPath = Storage::disk('local')->path($path);
            
            // 2. Capturar el comentario que viene de Vue
            $comentario = $request->input('comentario', 'Sin comentarios');

            // 3. Definir ruta del script
            $scriptPath = base_path('scripts/vision_analyzer_coment.py');
            
            // 4. EJECUTAR PROCESO (Pasamos imagen Y comentario como argumentos)
            $process = Process::timeout(60)->run([
                'python3', 
                $scriptPath, 
                $fullPath, 
                $comentario
            ]);
            
            $output = $process->output();
            $error = $process->errorOutput();

            // 5. Limpiar disco
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            // 6. Procesar JSON de salida
            $start = strpos($output, '{');
            $end = strrpos($output, '}');

            if ($start !== false && $end !== false) {
                $jsonStr = substr($output, $start, $end - $start + 1);
                $data = json_decode($jsonStr, true);
                
                if ($data) {
                    return response()->json($data);
                }
            }

            if ($error) {
                Log::error("Python Vision Error: " . $error);
            }

            return response()->json([
                'status' => 'error',
                'raza_detectada' => 'Desconocida',
                'sugerencia' => 'Error al procesar la respuesta de la IA.'
            ]);

        } catch (\Exception $e) {
            Log::error("Vision Controller Exception: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'sugerencia' => 'Hubo un problema en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }


    
}
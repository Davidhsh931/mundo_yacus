<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VisionController extends Controller
{
    public function analyze(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|max:2048'
            ]);

            // 1. Guardar la imagen
            $path = $request->file('image')->store('temp_vision', 'local');
            $fullPath = Storage::disk('local')->path($path);

            // 2. Ejecutar el script con un tiempo de espera más largo (timeout)
            // YOLO puede tardar un poco más que MobileNet, especialmente la primera vez.
            $scriptPath = base_path('scripts/vision_analyzer.py');
            
            // Aumentamos a 60 segundos por si acaso
            $process = Process::timeout(60)->run("python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($fullPath));
            
            $output = $process->output();
            $error = $process->errorOutput();

            // 3. Limpiar el archivo temporal
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            // 4. Procesar la respuesta JSON de Python
            $start = strpos($output, '{');
            $end = strrpos($output, '}');

            if ($start !== false && $end !== false) {
                $jsonStr = substr($output, $start, $end - $start + 1);
                $data = json_decode($jsonStr, true);
                
                if ($data) {
                    // Retornamos TODO el array de Python (incluyendo coordenadas y status)
                    return response()->json($data);
                }
            }

            // Si hay un error en la salida de Python, loguearlo para debug
            if ($error) {
                Log::error("Python Vision Error: " . $error);
            }

            return response()->json([
                'status' => 'error',
                'raza_detectada' => 'Desconocida',
                'confianza' => '0%',
                'sugerencia' => 'La IA no devolvió un formato válido. Revisa los logs del servidor.'
            ]);

        } catch (\Exception $e) {
            Log::error("Vision Controller Exception: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'raza_detectada' => 'Error Crítico',
                'confianza' => '0%',
                'sugerencia' => 'Hubo un problema en el servidor: ' . $e->getMessage()
            ], 500);
        }
        // En VisionController.php
        $comentario = $request->input('comentario', 'Nada');

// Pasamos los 2 argumentos al comando
$process = Process::timeout(60)->run("python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($fullPath) . " " . escapeshellarg($comentario));
    }
}
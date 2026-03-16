<!---Scikit-Learn y Pythorch:---->
<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

// --- Lógica de IA ---
const visionResult = ref(null);
const loadingVision = ref(false);
const previewUrl = ref(null);

// Referencia para la imagen y calcular escalas del cuadro
const imageElement = ref(null);

const analyzeImage = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    previewUrl.value = URL.createObjectURL(file);
    visionResult.value = null; 
    loadingVision.value = true;

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await axios.post('/vision/analyze', formData);
        visionResult.value = response.data;
    } catch (error) {
        console.error("Error en Cuy-Vision:", error);
        visionResult.value = {
            status: 'error',
            raza_detectada: 'Error',
            confianza: '0%',
            sugerencia: 'No se pudo conectar con el servidor de IA.'
        };
    } finally {
        loadingVision.value = false;
    }
};

// --- Cálculo dinámico del cuadro de YOLO ---
const boundingBoxStyle = computed(() => {
    // Solo calculamos si hay coordenadas y la imagen ya cargó
    if (!visionResult.value?.coordenadas || !imageElement.value) return {};

    const [x1, y1, x2, y2] = visionResult.value.coordenadas;
    const img = imageElement.value;

    // Escala: Relación entre tamaño real de la foto y tamaño mostrado en pantalla
    const scaleX = img.clientWidth / img.naturalWidth;
    const scaleY = img.clientHeight / img.naturalHeight;

    return {
        left: `${x1 * scaleX}px`,
        top: `${y1 * scaleY}px`,
        width: `${(x2 - x1) * scaleX}px`,
        height: `${(y2 - y1) * scaleY}px`,
    };
});
</script>

<template>
    <div class="p-6 bg-white rounded-lg shadow">
        
        <div class="mt-4"> 
            <label class="block font-medium text-sm text-gray-700 mb-2">Foto del Cuy</label>
            
            <input 
                type="file" 
                @change="analyzeImage" 
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
            />

            <div v-if="previewUrl" class="mt-4 relative inline-block">
                <img 
                    ref="imageElement"
                    :src="previewUrl" 
                    class="max-w-xs h-auto rounded-lg border-2 border-indigo-100 shadow-sm block" 
                />
                
                <div v-if="visionResult?.status === 'success' && visionResult?.coordenadas"
                     :style="boundingBoxStyle"
                     class="absolute border-2 border-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)] pointer-events-none rounded-sm">
                    <span class="absolute -top-6 left-0 bg-green-500 text-white text-[10px] px-1 py-0.5 rounded whitespace-nowrap">
                        Cuy Detectado {{ visionResult.confianza }}
                    </span>
                </div>
            </div>

            <div v-if="loadingVision" class="mt-4 flex items-center text-indigo-600 font-medium animate-pulse">
                <svg class="animate-spin h-5 w-5 mr-3 border-t-2 border-indigo-600 rounded-full" viewBox="0 0 24 24"></svg>
                Analizando con Cuy-Vision AI (YOLOv8)...
            </div>

            <div v-if="visionResult" 
                 class="mt-4 p-4 rounded-lg border transition-colors duration-300"
                 :class="[
                     visionResult.status === 'success' ? 'bg-green-50 border-green-200' : 
                     visionResult.status === 'error' ? 'bg-red-50 border-red-200' : 'bg-indigo-50 border-indigo-200'
                 ]">
                
                <h4 class="font-bold flex items-center" 
                    :class="visionResult.status === 'success' ? 'text-green-800' : visionResult.status === 'error' ? 'text-red-800' : 'text-indigo-800'">
                    <span class="mr-2">{{ visionResult.status === 'success' ? '✅' : '🤖' }}</span>
                    Resultado de Análisis IA:
                </h4>

                <div class="grid grid-cols-2 gap-2 mt-2">
                    <p class="text-sm" :class="visionResult.status === 'success' ? 'text-green-700' : 'text-indigo-600'">
                        <b>Raza:</b> {{ visionResult?.raza_detectada }}
                    </p>
                    <p class="text-sm" :class="visionResult.status === 'success' ? 'text-green-700' : 'text-indigo-600'">
                        <b>Confianza:</b> {{ visionResult?.confianza }}
                    </p>
                </div>

                <div class="mt-2 text-xs p-2 rounded"
                     :class="visionResult.status === 'success' ? 'text-green-700 bg-white/60' : 'text-red-700 bg-white/60'">
                    ✨ {{ visionResult?.sugerencia }}
                </div>
            </div>
        </div>

    </div>
</template>
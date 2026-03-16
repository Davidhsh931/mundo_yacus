<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const visionResult = ref(null);
const loadingVision = ref(false);
const previewUrl = ref(null);
const userComment = ref(""); 
const imageElement = ref(null);

const analyzeImage = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    previewUrl.value = URL.createObjectURL(file);
    visionResult.value = null; 
    loadingVision.value = true;

    const formData = new FormData();
    formData.append('image', file);
    formData.append('comentario', userComment.value);

    try {
        const response = await axios.post('/vision/analyze/coment', formData);
        visionResult.value = response.data;
    } catch (error) {
        visionResult.value = { status: 'error', raza_detectada: 'Error', sugerencia: 'Fallo de conexión.' };
    } finally {
        loadingVision.value = false;
    }
};

const boundingBoxStyle = computed(() => {
    if (!visionResult.value?.coordenadas || !imageElement.value || loadingVision.value) return {};

    const [x1, y1, x2, y2] = visionResult.value.coordenadas;
    const img = imageElement.value;

    // Cálculo proporcional basado en el tamaño renderizado actual
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
    <div class="p-6 bg-slate-50 rounded-xl shadow-lg max-w-2xl mx-auto border border-slate-200">
        <header class="mb-6">
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-2">
                <span class="bg-indigo-600 text-white p-2 rounded-lg text-sm shadow-sm">PRO</span>
                Cuy-Vision AI Scanner
            </h2>
            <p class="text-slate-500 text-sm italic">YOLOv8 Precise Detection + NLP Health Analysis</p>
        </header>
        
        <div class="space-y-6"> 
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100">
                <label class="block font-bold text-slate-700 mb-2 text-sm uppercase tracking-wider">Observaciones de Salud</label>
                <textarea 
                    v-model="userComment" 
                    placeholder="Ej: El ejemplar presenta decaimiento y poco apetito..."
                    class="w-full border-slate-200 rounded-lg p-3 text-sm focus:ring-2 focus:ring-indigo-500 transition-all resize-none h-24 mb-4"
                ></textarea>

                <label class="group inline-flex items-center justify-center w-full px-4 py-4 bg-indigo-600 text-white font-bold rounded-xl cursor-pointer hover:bg-indigo-700 transition-all shadow-md hover:shadow-indigo-200">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                    INICIAR ESCANEO DE IMAGEN
                    <input type="file" @change="analyzeImage" class="hidden" />
                </label>
            </div>

            <div v-if="previewUrl || loadingVision" class="relative bg-slate-900 rounded-2xl overflow-hidden shadow-2xl min-h-[350px] flex items-center justify-center p-8 border-4 border-slate-800">
                
                <div class="relative inline-block">
                    <img 
                        ref="imageElement" 
                        :src="previewUrl" 
                        class="max-h-[450px] w-auto rounded-lg transition-all duration-700 shadow-2xl"
                        :class="loadingVision ? 'opacity-30 blur-md scale-95' : 'opacity-100'"
                    />

                    <div v-if="loadingVision" class="absolute inset-0 overflow-hidden rounded-lg">
                        <div class="w-full h-1 bg-indigo-400 absolute top-0 animate-scan shadow-[0_0_20px_#818cf8]"></div>
                    </div>

                    <div v-if="visionResult?.status === 'success' && !loadingVision" 
                         :style="boundingBoxStyle" 
                         class="absolute border-[3px] border-green-400 pointer-events-none shadow-[0_0_15px_rgba(74,222,128,0.7)] z-10 transition-all duration-500">
                        <div class="absolute -top-8 left-[-3px] bg-green-500 text-white text-[11px] font-black px-2 py-1 rounded-t-sm flex items-center gap-2 whitespace-nowrap shadow-lg">
                            <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                            {{ visionResult.raza_detectada.toUpperCase() }} | {{ visionResult.confianza }}
                        </div>
                    </div>
                </div>

                <div v-if="loadingVision" class="absolute inset-0 flex items-center justify-center bg-slate-900/20 backdrop-blur-sm">
                    <div class="flex flex-col items-center gap-4 bg-white/10 p-6 rounded-2xl border border-white/20 shadow-2xl">
                        <svg class="animate-spin h-10 w-10 text-indigo-400" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-indigo-200 font-black text-xs tracking-[0.2em] animate-pulse">EXTRAYENDO DATOS BIOMÉTRICOS</span>
                    </div>
                </div>
            </div>

            <div v-if="visionResult && !loadingVision" class="grid grid-cols-1 md:grid-cols-2 gap-4 animate-fade-in">
                <div class="bg-white p-5 rounded-2xl border-b-4 border-blue-500 shadow-md">
                    <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Calidad Óptica</p>
                    <p class="text-2xl font-mono font-bold text-slate-800">{{ visionResult.nitidez }}</p>
                    <p class="text-[10px] text-blue-500 font-bold mt-1">✓ Procesado por SciPy Laplace</p>
                </div>

                <div class="bg-white p-5 rounded-2xl border-b-4 shadow-md transition-all"
                     :class="(visionResult.analisis_salud?.includes('ALERTA') || visionResult.analisis_salud?.includes('URGENTE')) ? 'border-red-500' : 'border-green-500'">
                    <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest flex items-center gap-2">
                        <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
                        Diagnóstico NLP (NLTK)
                    </p>
                    <p class="text-sm mt-2 leading-relaxed font-semibold italic" 
                       :class="(visionResult.analisis_salud?.includes('ALERTA') || visionResult.analisis_salud?.includes('URGENTE')) ? 'text-red-600' : 'text-slate-700'">
                        "{{ visionResult.analisis_salud }}"
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes scan {
    0% { top: 0; }
    100% { top: 100%; }
}
.animate-scan {
    animation: scan 2.5s ease-in-out infinite;
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
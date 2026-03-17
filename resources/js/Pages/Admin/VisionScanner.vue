<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, nextTick } from 'vue';
import axios from 'axios';

const imageInput = ref(null);
const cameraStream = ref(null);
const videoPlayer = ref(null);
const canvasOutput = ref(null);
const isCameraActive = ref(false);

const state = reactive({
    isLoading: false,
    imagePreview: null,
    comment: '',
    error: null,
    result: null, // Guardará la respuesta de la IA
});

// Referencias para el cuadro de coordenadas de YOLO
const yoloBox = ref({ x1: 0, y1: 0, x2: 0, y2: 0 });
const containerRef = ref(null);

// --- Funciones de Cámara ---
const startCamera = async () => {
    try {
        state.imagePreview = null;
        state.result = null;
        state.error = null;
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        cameraStream.value = stream;
        videoPlayer.value.srcObject = stream;
        isCameraActive.value = true;
    } catch (err) {
        state.error = "No se pudo acceder a la cámara. Verifica los permisos.";
        console.error("Camera error:", err);
    }
};

const takePhoto = () => {
    const video = videoPlayer.value;
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    state.imagePreview = canvas.toDataURL('image/jpeg');
    stopCamera();
};

const stopCamera = () => {
    if (cameraStream.value) {
        cameraStream.value.getTracks().forEach(track => track.stop());
        cameraStream.value = null;
        isCameraActive.value = false;
    }
};

// --- Funciones de Archivo ---
const triggerFileInput = () => imageInput.value.click();

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        state.error = null;
        state.result = null;
        const reader = new FileReader();
        reader.onload = (e) => state.imagePreview = e.target.result;
        reader.readAsDataURL(file);
    }
};

const dataURLtoFile = (dataurl, filename) => {
    let arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){ u8arr[n] = bstr.charCodeAt(n); }
    return new File([u8arr], filename, {type:mime});
};

// --- Funciones de Análisis (IA) ---
const analyzeImage = async () => {
    if (!state.imagePreview) {
        state.error = "Por favor, toma una foto o sube una imagen primero.";
        return;
    }

    state.isLoading = true;
    state.error = null;
    state.result = null;
    yoloBox.value = { x1: 0, y1: 0, x2: 0, y2: 0 }; // Reiniciar cuadro

    try {
        const file = dataURLtoFile(state.imagePreview, 'cuy_analysis.jpg');
        const formData = new FormData();
        formData.append('image', file);

        let response;

        if (state.comment.trim()) {
            // FLUJO 2: IA AVANZADA (Con Comentario y Salud)
            formData.append('comentario', state.comment);
            response = await axios.post('/vision/analyze/coment', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            // FLUJO 1: IA SIMPLE (Solo Detección de Raza)
            response = await axios.post('/vision/analyze', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }

        const data = response.data;
        if (data.status === 'success') {
            state.result = data;
            
            // --- Calcular Cuadro de YOLO Dinámico ---
            if (data.coordenadas && containerRef.value) {
                await nextTick(); // Esperar a que el DOM se actualice
                const containerWidth = containerRef.value.offsetWidth;
                const containerHeight = containerRef.value.offsetHeight;

                // Coordenadas normalizadas (de YOLO [0-1]) -> Coordenadas de pantalla (px)
                yoloBox.value = {
                    x1: data.coordenadas[0] * containerWidth,
                    y1: data.coordenadas[1] * containerHeight,
                    x2: data.coordenadas[2] * containerWidth,
                    y2: data.coordenadas[3] * containerHeight,
                };
            }

        } else {
            state.error = data.sugerencia || "La IA no pudo procesar la imagen.";
        }

    } catch (err) {
        state.error = "Error de conexión con el servidor de IA.";
        console.error("AI analysis error:", err);
    } finally {
        state.isLoading = false;
    }
};
</script>

<template>
    <Head title="Cuy-Vision AI Scanner" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cuy-Vision: Diagnóstico de IA Directo</h2>
        </template>

        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div ref="containerRef" class="bg-black aspect-video rounded-xl shadow-inner relative overflow-hidden flex items-center justify-center border-4 border-indigo-100">
                    
                    <video ref="videoPlayer" v-show="isCameraActive" autoplay playsinline class="absolute inset-0 w-full h-full object-cover"></video>

                    <img :src="state.imagePreview" v-if="state.imagePreview" class="absolute inset-0 w-full h-full object-contain" />

                    <div v-if="yoloBox.x1 !== yoloBox.x2" 
                        class="absolute border-4 border-lime-400 rounded-sm pointer-events-none z-10"
                        :style="{
                            left: yoloBox.x1 + 'px',
                            top: yoloBox.y1 + 'px',
                            width: (yoloBox.x2 - yoloBox.x1) + 'px',
                            height: (yoloBox.y2 - yoloBox.y1) + 'px'
                        }"
                    >
                        <span class="absolute -top-6 left-0 bg-lime-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.5 rounded-t-sm uppercase">
                            {{ state.result?.raza_detectada || 'Cuy' }}
                        </span>
                    </div>

                    <div v-if="!state.imagePreview && !isCameraActive" class="text-center text-gray-500 p-8 space-y-4">
                        <span class="text-8xl">🐹🔍</span>
                        <p class="font-medium">Cuy-Vision está listo para certificar la salud de tus cuyes.</p>
                        <p class="text-sm">Usa los botones de abajo para empezar.</p>
                    </div>

                    <div v-if="state.isLoading" class="absolute inset-0 bg-black/70 flex items-center justify-center z-20">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-200"></div>
                        <span class="ml-4 text-white text-lg font-medium">Analizando en tiempo real...</span>
                    </div>

                    <div v-if="state.error" class="absolute bottom-4 left-4 right-4 bg-red-100 text-red-800 p-3 rounded-lg text-sm border border-red-200 z-20">
                        {{ state.error }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded-xl shadow-sm">
                    <div class="space-y-4">
                        <h3 class="font-bold text-gray-700">Capturar Cuy</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="startCamera" v-if="!isCameraActive" class="bg-indigo-600 text-white p-3 rounded-lg font-bold text-sm hover:bg-indigo-700">Usar Cámara 📸</button>
                            <button @click="takePhoto" v-else class="bg-green-600 text-white p-3 rounded-lg font-bold text-sm hover:bg-green-700">Tomar Foto 🎯</button>
                            <button @click="triggerFileInput" class="bg-gray-200 text-gray-700 p-3 rounded-lg font-bold text-sm hover:bg-gray-300">Subir Imagen 📁</button>
                            <input type="file" ref="imageInput" @change="handleFileChange" accept="image/*" class="hidden" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="font-bold text-gray-700">Añadir Comentario de Salud (Opcional)</h3>
                        <textarea v-model="state.comment" rows="2" placeholder="Ej: 'El cuy Tipo 1 está decaído y tiene diarrea'." class="w-full border-gray-300 rounded-lg shadow-sm text-sm focus:border-indigo-400 focus:ring-indigo-100"></textarea>
                        <p class="text-xs text-gray-400 italic">Si añades un comentario, la IA activará el análisis de síntomas Avanzado.</p>
                    </div>

                    <button @click="analyzeImage" :disabled="!state.imagePreview || state.isLoading" class="w-full md:col-span-2 bg-gradient-to-r from-orange-400 to-orange-600 text-white p-4 rounded-xl font-black text-xl hover:from-orange-500 hover:to-orange-700 disabled:from-gray-300 disabled:to-gray-400 transition transform hover:scale-[1.01] active:scale-95">
                        {{ state.comment ? '🔬 Certificar Salud (Avanzado)' : '🔍 Análisis Rápido (IA)' }}
                    </button>
                </div>

                <div v-if="state.result" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-6 rounded-xl shadow-sm border-t-4 border-lime-400">
                    <div class="space-y-3">
                        <h4 class="font-bold text-lg text-gray-800">Diagnóstico de Raza</h4>
                        <div class="flex items-center gap-3">
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full font-bold text-sm">Raza: {{ state.result.raza_detectada }}</span>
                            <span class="bg-lime-100 text-lime-800 px-3 py-1 rounded-full font-bold text-sm">Confianza: {{ state.result.confianza }}</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ state.result.sugerencia }}</p>
                    </div>

                    <div class="space-y-3 border-l pl-6 border-gray-100" v-if="state.comment">
                        <h4 class="font-bold text-lg text-gray-800">Certificación de Salud (NLTK)</h4>
                        <p v-if="state.result.analisis_salud" class="bg-blue-50 text-blue-900 p-4 rounded-lg text-sm font-medium border border-blue-100">
                            {{ state.result.analisis_salud }}
                        </p>
                        <p v-else class="text-sm text-gray-500 italic">No se detectaron síntomas evidentes en el comentario.</p>
                        <span class="text-xs text-gray-400">Nitidez de imagen detectada: {{ state.result.nitidez }}</span>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
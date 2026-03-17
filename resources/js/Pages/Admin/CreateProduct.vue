<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// --- Estado del Formulario ---
const form = useForm({
  name: '',
  species: 'cuy', // Nuevo: Cuy, Oveja, Gallina, etc.
  price: '',
  product_state: 'vivo',
  custom_attributes: [{ key: '', value: '' }],
  ia_verification: null, // Guardaremos el resultado de la IA aquí
  image: null
});

// --- Lógica de IA (Cuy-Vision / Animal-Vision) ---
const visionResult = ref(null);
const loadingVision = ref(false);
const previewUrl = ref(null);
const imageElement = ref(null);

const analyzeImage = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    form.image = file; // Guardamos para el envío final
    previewUrl.value = URL.createObjectURL(file);
    visionResult.value = null; 
    loadingVision.value = true;

    const formData = new FormData();
    formData.append('image', file);

    try {
        // Usamos tu script de IA para validar el animal
        const response = await axios.post('/vision/analyze', formData);
        visionResult.value = response.data;
        form.ia_verification = response.data; // El sello de garantía
    } catch (error) {
        console.error("Error en Visión IA:", error);
    } finally {
        loadingVision.value = false;
    }
};

// --- Manejo de Atributos ---
const addAttribute = () => form.custom_attributes.push({ key: '', value: '' });
const removeAttribute = (index) => form.custom_attributes.splice(index, 1);

const submit = () => {
  form.post(route('guinea-pigs.store'));
};

// Estilo dinámico para el cuadro de detección
const boundingBoxStyle = computed(() => {
    if (!visionResult.value?.coordenadas || !imageElement.value) return {};
    const [x1, y1, x2, y2] = visionResult.value.coordenadas;
    const img = imageElement.value;
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
  <Head title="Publicar Producto" />
  <AuthenticatedLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🛒 Publicar en Mercado Mundo Yacus</h2>
    </template>

    <div class="max-w-5xl mx-auto p-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
          <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold mb-4 text-indigo-600 flex items-center">
              <span class="mr-2">🤖</span> Escáner de Calidad
            </h3>
            
            <div class="relative w-full aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                <img v-if="previewUrl" :src="previewUrl" ref="imageElement" class="w-full h-full object-cover" />
                <div v-if="visionResult" :style="boundingBoxStyle" class="absolute border-4 border-green-500 shadow-[0_0_15px_rgba(34,197,94,0.5)] pointer-events-none">
                    <span class="absolute -top-8 left-0 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                        {{ visionResult.raza_detectada }} ({{ visionResult.confianza }})
                    </span>
                </div>
                <div v-if="!previewUrl" class="flex items-center justify-center h-full text-gray-400 text-center p-4">
                    Tome una foto al animal para validar su calidad
                </div>
            </div>

            <input type="file" @change="analyzeImage" class="mt-4 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            
            <div v-if="loadingVision" class="mt-4 text-center text-indigo-600 font-medium animate-pulse">
                Analizando con IA...
            </div>
          </div>
        </div>

        <div class="lg:col-span-2 bg-white p-8 rounded-xl shadow-sm border border-gray-100">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Especie / Animal</label>
                <select v-model="form.species" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500">
                  <option value="cuy">🐹 Cuy</option>
                  <option value="oveja">🐑 Oveja</option>
                  <option value="gallina">🐔 Gallina</option>
                  <option value="cerdo">🐷 Cerdo</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Lote/Animal</label>
                <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ej: Cuy Tipo 1 - Orgánico">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Precio Unitario (S/.)</label>
                <input v-model="form.price" type="number" step="0.10" class="w-full border-gray-300 rounded-lg shadow-sm">
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Estado del Producto</label>
                <select v-model="form.product_state" class="w-full border-gray-300 rounded-lg shadow-sm">
                  <option value="vivo">Vivo (Cría/Reproductor)</option>
                  <option value="beneficiado">Beneficiado (Carne fresca)</option>
                  <option value="procesado">Procesado (Empacado al vacío)</option>
                </select>
              </div>
            </div>

            <div class="mb-8 p-6 bg-indigo-50 rounded-xl border-2 border-indigo-100">
              <h3 class="font-bold text-indigo-900 mb-2 flex items-center">
                📋 Ficha de la Chacra
              </h3>
              <p class="text-xs text-indigo-600 mb-4">Agregue peso, dieta, edad o cualquier detalle que valore su producto.</p>

              <div v-for="(attr, index) in form.custom_attributes" :key="index" class="flex gap-3 mb-3">
                <input v-model="attr.key" placeholder="Propiedad (ej: Peso)" class="flex-1 border-gray-300 rounded-lg text-sm shadow-sm">
                <input v-model="attr.value" placeholder="Valor (ej: 1.5kg)" class="flex-1 border-gray-300 rounded-lg text-sm shadow-sm">
                <button @click.prevent="removeAttribute(index)" class="text-red-400 hover:text-red-600 font-bold">✕</button>
              </div>

              <button @click.prevent="addAttribute" class="mt-2 text-indigo-700 text-sm font-bold hover:underline">+ Añadir detalle técnico</button>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white font-black py-4 rounded-xl hover:bg-indigo-700 transition-all shadow-lg flex items-center justify-center">
              🚀 Publicar en Mercado Mundo Yacus
            </button>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
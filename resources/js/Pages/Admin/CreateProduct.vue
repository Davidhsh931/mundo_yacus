<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

// --- Estado del Formulario ---
const form = useForm({
  name: '',
  species: 'cuy', 
  price: '',
  stock: 1, 
  product_state: 'vivo',
  custom_attributes: [{ key: '', value: '' }],
  ia_verification: null, 
  image: null
});

// --- Lógica de IA ---
const visionResult = ref(null);
const loadingVision = ref(false);
const previewUrl = ref(null);
const imageElement = ref(null);

const analyzeImage = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    form.image = file; 
    previewUrl.value = URL.createObjectURL(file);
    visionResult.value = null; 
    loadingVision.value = true;

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await axios.post('/vision/analyze', formData);
        visionResult.value = response.data;
        form.ia_verification = response.data; 
    } catch (error) {
        console.error("Error en Visión IA:", error);
    } finally {
        loadingVision.value = false;
    }
};

const addAttribute = () => form.custom_attributes.push({ key: '', value: '' });
const removeAttribute = (index) => form.custom_attributes.splice(index, 1);

const submit = () => {
  form.transform((data) => ({
    ...data,
    specifications: data.custom_attributes, 
  })).post(route('guinea-pigs.store'), {
    forceFormData: true,
    onSuccess: () => {
      alert('¡Publicado con éxito! 🚀');
      form.reset();
      previewUrl.value = null;
      visionResult.value = null;
    },
    onError: (errors) => {
      console.log("Errores del servidor:", errors);
    }
  });
};

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
    <div class="w-full">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
          <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
            <h3 class="text-md font-bold mb-4 text-indigo-600 flex items-center">
              <span class="mr-2">🤖</span> Escáner de Calidad
            </h3>
            
            <div class="relative w-full aspect-square bg-white rounded-lg overflow-hidden border-2 border-dashed border-gray-300">
                <img v-if="previewUrl" :src="previewUrl" ref="imageElement" class="w-full h-full object-cover" />
                <div v-if="visionResult" :style="boundingBoxStyle" class="absolute border-4 border-green-500 shadow-[0_0_15px_rgba(34,197,94,0.5)] pointer-events-none">
                    <span class="absolute -top-8 left-0 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                        {{ visionResult.raza_detectada }} ({{ visionResult.confianza }})
                    </span>
                </div>
                <div v-if="!previewUrl" class="flex items-center justify-center h-full text-gray-400 text-center p-4">
                    Seleccione una foto
                </div>
            </div>

            <input type="file" @change="analyzeImage" class="mt-4 w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
            
            <div v-if="loadingVision" class="mt-4 text-center text-indigo-600 font-medium animate-pulse text-sm">
                Analizando con IA...
            </div>
          </div>
        </div>

        <div class="lg:col-span-2">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Especie</label>
                <select v-model="form.species" class="w-full border-gray-300 rounded-lg text-sm shadow-sm">
                  <option value="cuy">🐹 Cuy</option>
                  <option value="oveja">🐑 Oveja</option>
                  <option value="gallina">🐔 Gallina</option>
                  <option value="cerdo">🐷 Cerdo</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Nombre del Lote</label>
                <input v-model="form.name" type="text" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Ej: Cuy Tipo 1">
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Precio (S/.)</label>
                <input v-model="form.price" type="number" step="0.10" class="w-full border-gray-300 rounded-lg text-sm">
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Stock</label>
                <input v-model="form.stock" type="number" min="0" class="w-full border-gray-300 rounded-lg text-sm">
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Estado</label>
                <select v-model="form.product_state" class="w-full border-gray-300 rounded-lg text-sm">
                  <option value="vivo">Vivo</option>
                  <option value="beneficiado">Beneficiado</option>
                </select>
              </div>
            </div>

            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
              <h3 class="text-xs font-bold text-gray-700 mb-2">📋 Ficha Técnica</h3>
              <div v-for="(attr, index) in form.custom_attributes" :key="index" class="flex gap-2 mb-2">
                <input v-model="attr.key" placeholder="Propiedad" class="flex-1 border-gray-300 rounded-lg text-xs">
                <input v-model="attr.value" placeholder="Valor" class="flex-1 border-gray-300 rounded-lg text-xs">
                <button @click.prevent="removeAttribute(index)" class="text-red-400 font-bold">✕</button>
              </div>
              <button @click.prevent="addAttribute" class="text-indigo-600 text-xs font-bold hover:underline">+ Añadir detalle</button>
            </div>

            <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition-all shadow-md disabled:opacity-50">
              {{ form.processing ? 'Publicando...' : '🚀 Publicar Ahora' }}
            </button>
          </form>
        </div>
      </div>
    </div>
</template>
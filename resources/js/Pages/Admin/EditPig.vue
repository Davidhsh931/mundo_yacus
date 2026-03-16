<script setup>
import { ref } from 'vue';
import axios from 'axios';
import SalesChart from '@/Components/SalesChart.vue'; 

const props = defineProps({
    pig: Object
});

const stockSugerido = ref(null);
const cargando = ref(false);

const ejecutarAnalisis = async () => {
    cargando.value = true;
    stockSugerido.value = null; 
    
    try {
        const response = await axios.get(`/api/cuy/sugerir-stock/${props.pig.id}`);
        // Limpiamos la respuesta para evitar el texto plano en la UI
        let rawData = typeof response.data === 'string' ? JSON.parse(response.data) : response.data;
        if (typeof rawData === 'string') rawData = JSON.parse(rawData);

        stockSugerido.value = rawData;
    } catch (error) {
        console.error("Error en la IA:", error);
        alert("Error: La IA no pudo procesar los datos.");
    } finally {
        cargando.value = false;
    }
};
</script>

<template>
    <div class="mb-6 p-6 bg-white border-2 border-indigo-500 rounded-xl shadow-md overflow-hidden relative">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-indigo-900 font-black flex items-center gap-2 text-lg">
                    <span class="text-2xl">🐼</span> Mundo Yacus AI
                </h3>
                <p class="text-sm text-gray-600 mt-1">Análisis predictivo con Scikit-learn</p>
            </div>
            
            <button 
                @click="ejecutarAnalisis"
                :disabled="cargando"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-full font-bold transition-all transform active:scale-95 disabled:opacity-50"
            >
                {{ cargando ? 'Calculando...' : 'Predecir Stock' }}
            </button>
        </div>

        <div v-if="stockSugerido || cargando" class="mt-4 pt-4 border-t border-indigo-100">
            <div v-if="cargando" class="flex items-center gap-3">
                <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-indigo-600 font-medium animate-pulse">Consultando a Scikit-learn...</p>
            </div>

            <div v-if="stockSugerido && !cargando" class="flex flex-col w-full gap-6">
                <div class="flex items-center gap-6">
                    <div class="bg-indigo-100 p-4 rounded-lg shadow-inner">
                        <p class="text-xs text-indigo-700 font-bold uppercase tracking-widest mb-1">Sugerencia</p>
                        <p class="text-5xl font-black text-indigo-600">
                            {{ stockSugerido.stock_sugerido }} 
                            <span class="text-sm font-normal text-indigo-400 uppercase">unidades</span>
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">
                                🚀 {{ stockSugerido.metodo }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed italic">
                            Basado en <strong>{{ stockSugerido.registros }}</strong> ventas históricas.
                        </p>
                    </div>
                </div>

                <div v-if="stockSugerido.chart_data" class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-4 tracking-tighter">Tendencia Mensual de Ventas</p>
                    <SalesChart 
                        :labels="stockSugerido.chart_data.labels" 
                        :data="stockSugerido.chart_data.values" 
                    />
                </div>
            </div>
        </div>
    </div>
</template>
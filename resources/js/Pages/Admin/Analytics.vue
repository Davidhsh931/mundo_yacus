<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SalesChart from '@/Components/SalesChart.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({
    stock_sugerido: 0,
    metodo: 'Cargando...',
    registros: 0,
    chart_data: { labels: [], values: [] }
});

const isLoading = ref(true);

const fetchPrediction = async () => {
    isLoading.value = true;
    try {
        // Llamada a tu API que ejecuta el script de Python
        const res = await axios.get('/api/cuy/sugerir-stock/1');
        stats.value = res.data;
    } catch (e) {
        console.error("Error en la predicción", e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchPrediction();
});
</script>

<template>
    <Head title="Mundo Yacus AI" />

    <AuthenticatedLayout>
        <div class="py-6 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-8 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                    <div>
                        <h1 class="text-2xl font-black text-indigo-900 flex items-center gap-2">
                            🐼 Mundo Yacus AI
                        </h1>
                        <p class="text-sm text-gray-500 font-medium">Análisis predictivo con Scikit-learn</p>
                    </div>
                    <button @click="fetchPrediction" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-full font-bold transition shadow-lg shadow-indigo-200">
                        Predecir Stock
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-indigo-50 p-6 rounded-2xl border-2 border-indigo-100 relative overflow-hidden">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-4">Sugerencia</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-6xl font-black text-indigo-600">{{ stats.stock_sugerido }}</span>
                            <span class="text-sm font-bold text-indigo-400 uppercase">Unidades</span>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-200/30 rounded-full"></div>
                    </div>

                    <div class="md:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 flex flex-col justify-center">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-green-100 text-green-700 text-[10px] px-2 py-1 rounded-md font-bold uppercase italic">
                                🚀 {{ stats.metodo }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm">
                            Basado en <span class="font-bold text-gray-900">{{ stats.registros }}</span> ventas históricas registradas en el sistema.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        📈 Tendencia Mensual de Ventas
                    </h3>
                    
                    <div v-if="isLoading" class="h-80 flex flex-col items-center justify-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mb-4"></div>
                        <p class="text-gray-400 font-bold animate-pulse text-xs">ENTRENANDO MODELO...</p>
                    </div>

                    <SalesChart v-else :data="stats.chart_data.values" :labels="stats.chart_data.labels" />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
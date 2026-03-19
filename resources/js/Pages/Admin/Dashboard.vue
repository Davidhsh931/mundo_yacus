<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SalesChart from '@/Components/SalesChart.vue'; 
import CreateProductForm from '@/Pages/Admin/CreateProduct.vue'; // Recuperamos el formulario
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    totalPigs: { type: Number, default: 0 },
    sales: { type: Number, default: 0 }
});

const stats = ref({
    stock_sugerido: '...',
    chart_data: { values: [], labels: [] }
});
const isLoading = ref(true);

// ESTA ES LA LÓGICA QUE HACE QUE EL BOTÓN NO SEA ADORNO
const ejecutarIA = async () => {
    isLoading.value = true;
    try {
        const res = await axios.get('/api/cuy/sugerir-stock/1'); 
        stats.value = res.data;
    } catch (e) {
        console.error("Error en Scikit-learn:", e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    ejecutarIA();
});
</script>

<template>
    <Head title="Panel de Productor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-xl text-gray-800">🚀 Centro de Mando</h2>
                <div class="flex gap-2">
                    <Link :href="route('admin.vision')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold">📸 Cuy-Vision</Link>
                    <Link :href="route('admin.analytics')" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg">🤖 Ver Análisis Completo</Link>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-50">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 shadow-sm rounded-xl border-t-4 border-green-500">
                        <h2 class="text-xs font-bold text-gray-400 uppercase">Cuyes en Granja</h2>
                        <p class="text-3xl font-black text-gray-800">{{ totalPigs }}</p>
                    </div>
                    
                    <div class="bg-white p-6 shadow-sm rounded-xl border-t-4 border-purple-500 relative">
                        <h2 class="text-xs font-bold text-gray-400 uppercase">IA: Stock Sugerido</h2>
                        <p class="text-3xl font-black text-purple-600">{{ stats.stock_sugerido }}</p>
                        <button @click="ejecutarIA" class="absolute top-4 right-4 text-[10px] bg-purple-100 text-purple-700 px-2 py-1 rounded-md font-bold hover:bg-purple-200">
                            🔄 RECALCULAR
                        </button>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-sm font-bold text-gray-700 mb-6 uppercase tracking-wider flex items-center gap-2">
                        📈 Tendencia Mensual (Regresión Lineal)
                    </h3>
                    <div v-if="isLoading" class="h-64 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                    </div>
                    <SalesChart v-else :data="stats.chart_data.values" :labels="stats.chart_data.labels" />
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm border-b-4 border-orange-500">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 italic">📦 Publicar Nuevo Producto en la Chacra</h3>
                    <CreateProductForm />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
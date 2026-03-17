<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SalesChart from '@/Components/SalesChart.vue'; 
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3'; // Importamos Link para los botones
import axios from 'axios';

const props = defineProps({
    totalPigs: { type: Number, default: 0 },
    totalOrders: { type: Number, default: 0 },
    totalClients: { type: Number, default: 0 },
    sales: { type: Number, default: 0 }
})

const sugerenciaGlobal = ref('Analizando...');
const chartData = ref([]);
const chartLabels = ref([]);
const isLoadingChart = ref(true);

onMounted(async () => {
    try {
        // Llamada a tu script de Scikit-learn
        const res = await axios.get('/api/cuy/sugerir-stock/1'); 
        sugerenciaGlobal.value = res.data.stock_sugerido + " unidades";
        chartData.value = res.data.chart_data.values;
        chartLabels.value = res.data.chart_data.labels;
    } catch (e) {
        sugerenciaGlobal.value = "Datos insuficientes";
    } finally {
        isLoadingChart.value = false;
    }
});
</script>

<template>
    <Head title="Panel de Productor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Centro de Mando - Mundo Yacus
                </h2>
                <Link :href="route('admin.vision')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition">
                    📸 Abrir Cuy-Vision
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-50">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8"> 
                    <div class="bg-white p-6 shadow-sm rounded-xl text-center border-t-4 border-green-500">
                        <h2 class="text-sm font-bold text-gray-500 uppercase">Cuyes en Granja</h2>
                        <p class="text-3xl font-black text-gray-800">{{ totalPigs }}</p>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-xl text-center border-t-4 border-blue-500">
                        <h2 class="text-sm font-bold text-gray-500 uppercase">Pedidos Hoy</h2>
                        <p class="text-3xl font-black text-gray-800">{{ totalOrders }}</p>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-xl text-center border-t-4 border-yellow-500">
                        <h2 class="text-sm font-bold text-gray-500 uppercase">Clientes Activos</h2>
                        <p class="text-3xl font-black text-gray-800">{{ totalClients }}</p>
                    </div>

                    <div class="bg-white p-6 shadow-sm rounded-xl text-center border-t-4 border-emerald-500">
                        <h2 class="text-sm font-bold text-gray-500 uppercase">Ingresos (S/)</h2>
                        <p class="text-3xl font-black text-gray-800">{{ sales }}</p>
                    </div>

                    <div class="bg-indigo-600 p-6 shadow-lg rounded-xl text-white text-center flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 opacity-10 text-5xl translate-x-2 -translate-y-2">🤖</div>
                        <h2 class="text-sm font-bold uppercase tracking-widest">🐼 Predicción IA</h2>
                        <p class="text-2xl font-black mt-2">{{ sugerenciaGlobal }}</p>
                        <span class="text-[10px] opacity-80 mt-1">Stock sugerido para próximo mes</span>
                    </div>
                </div>

                <div class="bg-white p-2 rounded-xl shadow-sm">
                    <div v-if="isLoadingChart" class="h-80 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    </div>
                    <SalesChart v-else :data="chartData" :labels="chartLabels" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
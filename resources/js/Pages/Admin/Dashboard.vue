<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

defineProps({
    totalPigs: Number,
    totalOrders: Number,
    totalClients: Number,
    sales: Number
})

const sugerenciaGlobal = ref('Analizando...');

onMounted(async () => {
    try {
        // Consultamos al cuy ID 1 como muestra para el dashboard
        const res = await axios.get('/api/cuy/sugerir-stock/1');
        sugerenciaGlobal.value = res.data.stock_sugerido + " unidades";
    } catch (e) {
        sugerenciaGlobal.value = "Datos insuficientes";
    }
});
</script>

<template>
    <div class="p-8">
        <h1 class="text-3xl font-bold mb-6">Panel Admin - Mundo Yacus</h1>

        <div class="grid grid-cols-5 gap-6"> <div class="bg-white p-6 shadow rounded text-center">
                <h2 class="text-lg">Cuyes</h2>
                <p class="text-2xl font-bold">{{ totalPigs }}</p>
            </div>

            <div class="bg-white p-6 shadow rounded text-center">
                <h2 class="text-lg">Pedidos</h2>
                <p class="text-2xl font-bold">{{ totalOrders }}</p>
            </div>

            <div class="bg-white p-6 shadow rounded text-center">
                <h2 class="text-lg">Clientes</h2>
                <p class="text-2xl font-bold">{{ totalClients }}</p>
            </div>

            <div class="bg-white p-6 shadow rounded text-center">
                <h2 class="text-lg">Ventas</h2>
                <p class="text-2xl font-bold">S/ {{ sales }}</p>
            </div>

            <div class="bg-indigo-600 p-6 shadow rounded text-white text-center border-b-4 border-indigo-900">
                <h2 class="text-lg font-bold">🐼 Predicción IA</h2>
                <p class="text-xl font-black mt-1">{{ sugerenciaGlobal }}</p>
                <span class="text-[10px] opacity-75">Sugerencia (Cuy #1)</span>
            </div>
        </div>
    </div>
</template>
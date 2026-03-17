<template>
    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-5 rounded-xl text-white shadow-lg">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-2xl">🤖</span>
            <h3 class="font-bold uppercase text-xs tracking-wider text-indigo-100">Sugerencia de la IA</h3>
        </div>
        <p class="text-lg font-medium leading-tight">
            {{ prediction || 'Analizando tendencias de mercado...' }}
        </p>
        <div class="mt-4 pt-3 border-t border-indigo-400/30 flex justify-between items-center text-[10px]">
            <span>BASADO EN PREDICT_STOCK.PY</span>
            <span class="bg-indigo-400/50 px-2 py-0.5 rounded">Mundo Yacus AI</span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const prediction = ref("");

onMounted(async () => {
    // Aquí llamamos a la ruta que creamos en el controlador anterior
    const response = await fetch('/admin/guinea-pigs/sugerir-stock/all');
    const data = await response.json();
    prediction.value = `Se estima un aumento de demanda. Te recomendamos preparar ${data.stock_sugerido || 5} unidades adicionales.`;
});
</script>
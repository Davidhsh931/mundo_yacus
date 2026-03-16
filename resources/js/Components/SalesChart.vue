<script setup>
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    data: { type: Array, default: () => [] },
    labels: { type: Array, default: () => [] }
});

const chartCanvas = ref(null);
let chartInstance = null;

const initChart = () => {
    // Si ya existe una instancia (por un re-render), la destruimos para evitar duplicados
    if (chartInstance) {
        chartInstance.destroy();
    }

    const ctx = chartCanvas.value.getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: props.labels,
            datasets: [{
                label: 'Ventas de Mundo Yacus',
                data: props.data,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#4f46e5',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
};

onMounted(() => {
    // Solo cargamos el script si no existe ya en el documento
    if (!window.Chart) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = initChart;
        document.head.appendChild(script);
    } else {
        initChart();
    }
});

// ESCUCHA: Si los datos de Laravel cambian, actualizamos la gráfica automáticamente
watch(() => props.data, () => {
    if (window.Chart) initChart();
}, { deep: true });
</script>

<template>
    <div class="w-full bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">
                📈 Tendencia de Ventas
            </h3>
        </div>
        <div style="position: relative; height: 320px;">
            <canvas ref="chartCanvas"></canvas>
        </div>
    </div>
</template>
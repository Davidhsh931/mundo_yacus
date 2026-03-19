<script setup>
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
    data: { type: Array, default: () => [] },
    labels: { type: Array, default: () => [] }
});

const chartCanvas = ref(null);
let chartInstance = null;

const initChart = () => {
    // Evitamos duplicados destruyendo la instancia previa
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
    // Carga dinámica de Chart.js para optimizar el rendimiento
    if (!window.Chart) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = initChart;
        document.head.appendChild(script);
    } else {
        initChart();
    }
});

// Vigilamos cambios profundos en los datos
watch(() => props.data, (newVal) => {
    if (newVal && newVal.length > 0) {
        initChart();
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div class="w-full bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">
                📈 Tendencia de Ventas (Datos de la Chacra)
            </h3>
        </div>
        <div style="position: relative; height: 320px;">
            <canvas ref="chartCanvas"></canvas>
        </div>
    </div>
</template>
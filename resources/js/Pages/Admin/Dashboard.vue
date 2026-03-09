<script setup>

defineProps({
    totalPigs:Number,
    totalOrders:Number,
    totalClients:Number,
    sales:Number
})

</script>

<template>

<div class="p-8">

<h1 class="text-3xl font-bold mb-6">
Panel Admin - Mundo Yacus
</h1>

<div class="grid grid-cols-4 gap-6">

<div class="bg-white p-6 shadow rounded">
<h2 class="text-lg">Cuyes</h2>
<p class="text-2xl font-bold">{{ totalPigs }}</p>
</div>

<div class="bg-white p-6 shadow rounded">
<h2 class="text-lg">Pedidos</h2>
<p class="text-2xl font-bold">{{ totalOrders }}</p>
</div>

<div class="bg-white p-6 shadow rounded">
<h2 class="text-lg">Clientes</h2>
<p class="text-2xl font-bold">{{ totalClients }}</p>
</div>

<div class="bg-white p-6 shadow rounded">
<h2 class="text-lg">Ventas</h2>
<p class="text-2xl font-bold">S/ {{ sales }}</p>
</div>

</div>

</div>

</template>
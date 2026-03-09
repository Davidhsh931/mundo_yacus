<script setup>

import AppLayout from './App.vue'
import ProductGallery from '../Components/ProductGallery.vue'

defineProps({
    pig:Object
})

</script>

<template>

<AppLayout>

<div class="grid grid-cols-2 gap-10">

    <!-- galería -->
    <ProductGallery :images="pig.images"/>

    <!-- información -->
    <div>

        <h1 class="text-3xl font-bold">
            {{ pig.name }}
        </h1>

        <p class="text-gray-500 mt-2">
            {{ pig.breed }}
        </p>

        <p class="text-2xl font-bold text-green-600 mt-4">
            S/ {{ pig.price }}
        </p>

        <p class="mt-3">
            Stock: {{ pig.stock }}
        </p>

    </div>

</div>

</AppLayout>

</template>
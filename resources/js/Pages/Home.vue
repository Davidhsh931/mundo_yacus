<!-- resources/js/Pages/Home.vue -->
<script setup>
import AppLayout from '@/Pages/App.vue'

defineProps({
  guineaPigs: Array
})
</script>

<template>
  <AppLayout>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div 
        v-for="pig in guineaPigs" 
        :key="pig.id"
        class="bg-white p-4 rounded shadow"
      >
        <h2 class="text-xl font-semibold">{{ pig.name }}</h2>
        <p class="text-gray-600">{{ pig.breed }}</p>
        <p class="mt-2 font-bold text-green-600">S/ {{ pig.price }}</p>
        <p class="text-sm text-gray-500">Stock: {{ pig.stock }}</p>
      </div>
    </div>
  </AppLayout>
</template>
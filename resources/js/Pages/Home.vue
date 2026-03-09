<!-- resources/js/Pages/Home.vue -->
<script setup>
import AppLayout from './App.vue'
import { router } from '@inertiajs/vue3'

defineProps({
  guineaPigs: Array
})

function addToCart(id){
    router.post('/cart/add/' + id)
}
</script>

<template>
  <AppLayout>
    <div v-if="$page.props.flash?.error" class="bg-red-200 text-red-800 p-3 rounded mb-6 border border-red-300">
      {{ $page.props.flash.error }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div 
        v-for="pig in guineaPigs" 
        :key="pig.id"
        @click="$inertia.visit('/product/' + pig.id)"
        class="bg-white p-4 rounded shadow hover:shadow-lg transition-shadow"
      >
        <img
          v-if="pig.images && pig.images.length"
          :src="pig.images[0].image_path"
          class="w-full h-48 object-cover rounded mb-3"
        />
        <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center rounded mb-3 text-gray-400">
          Sin foto
        </div>

        <h2 class="text-xl font-semibold">{{ pig.name }}</h2>
        <p class="text-gray-600">{{ pig.breed }}</p>
        <p class="mt-2 font-bold text-green-600">S/ {{ pig.price }}</p>
        
        <p :class="pig.stock <= 0 ? 'text-red-500 font-bold' : 'text-gray-500'" class="text-sm">
          Stock: {{ pig.stock }}
        </p>

        <button
          @click="addToCart(pig.id)"
          :disabled="pig.stock <= 0"
          class="w-full mt-3 bg-green-600 text-white px-3 py-2 rounded font-bold hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
        > 
          {{ pig.stock <= 0 ? 'Agotado' : 'Agregar al carrito' }}
        </button>
      </div>
    </div>
  </AppLayout>
</template>
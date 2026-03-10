<script setup>
import { router } from '@inertiajs/vue3'

const total = Object.values(props.cart || {}).reduce((sum, item) => {
    return sum + (item.price * item.quantity)
}, 0)

const props = defineProps({
    cart: Object
})

const remove = (id) => {
    router.post(`/cart/remove/${id}`)
}

const checkout = () => {
    router.post('/cart/checkout')
}
</script>

<template>

<h1 class="text-2xl font-bold mb-4">Carrito</h1>

<div v-if="!cart || Object.keys(cart).length === 0">
    Carrito vacío
</div>

<div v-else>

    <div v-for="(item,id) in cart" :key="id" class="border p-4 mb-3 flex items-center gap-4">
    <img v-if="item.image" :src="item.image" class="w-24 h-24 object-cover rounded" />
    
    <div>
        <h2 class="font-bold">{{ item.name }}</h2>
        <p>Precio: S/. {{ item.price }}</p>
        <p>Cantidad: {{ item.quantity }}</p>
    </div>

    <button
        @click="remove(id)"
        class="bg-red-500 text-white px-3 py-1 rounded"
    >Eliminar</button>
</div>
<div class="text-xl font-bold mt-4">
Total: S/. {{ total }}
</div>

    <button
        @click="checkout"
        class="bg-green-600 text-white px-4 py-2 rounded mt-4"
    >
        Comprar
    </button>

</div>

</template>
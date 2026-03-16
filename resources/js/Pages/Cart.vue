<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, Link, Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    cart: Object
})

const total = computed(() => {
    return Object.values(props.cart || {}).reduce((sum, item) => {
        return sum + (item.price * item.quantity)
    }, 0)
})

const remove = (id) => {
    router.post(`/cart/remove/${id}`)
}

const goToCheckout = () => {
    router.get('/checkout')
}
</script>

<template>
    <Head title="Carrito" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi Carrito 🐹</h2>
        </template>

        <div class="py-12 max-w-4xl mx-auto px-4">
            <div v-if="!cart || Object.keys(cart).length === 0" class="bg-white p-8 rounded-lg shadow text-center">
                <p class="text-gray-500 mb-6 text-lg">Tu carrito está vacío.</p>
                <Link href="/" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
                    Volver a la Tienda
                </Link>
            </div>

            <div v-else class="space-y-4">
                <div v-for="(item, id) in cart" :key="id" class="bg-white p-4 rounded-lg shadow flex items-center gap-4">
                    <img v-if="item.image" :src="item.image" class="w-24 h-24 object-cover rounded" />
                    <div class="flex-1">
                        <h2 class="font-bold text-xl">{{ item.name }}</h2>
                        <p class="text-green-600 font-bold text-lg">S/. {{ item.price }}</p>
                        <p class="text-sm text-gray-500">Cantidad: {{ item.quantity }}</p>
                    </div>
                    <button @click="remove(id)" class="text-red-500 font-bold px-4 py-2 hover:bg-red-50 rounded">
                        Eliminar
                    </button>
                </div>

                <div class="bg-white p-6 rounded-lg shadow mt-6">
                    <div class="flex justify-between items-center text-2xl font-bold mb-6">
                        <span>Total:</span>
                        <span class="text-indigo-600">S/. {{ total }}</span>
                    </div>
                    <button @click="goToCheckout" class="w-full bg-green-600 text-white py-4 rounded-xl font-bold text-xl hover:bg-green-700 shadow-lg transition">
                        Proceder al Pago
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    cart: Object,
    total: Number
});

const form = useForm({
    shipping_address: '',
    payment_method: 'yape',
});

const submit = () => {
    form.post('/cart/checkout');
};
</script>

<template>
    <Head title="Finalizar Compra" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Datos de Envío y Pago</h2>
        </template>

        <div class="py-12 max-w-2xl mx-auto px-4">
            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Dirección de Entrega</label>
                        <input v-model="form.shipping_address" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Calle Los Sauces 456, Surco" required>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-2">Método de Pago</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="form.payment_method = 'yape'" :class="form.payment_method === 'yape' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200'" class="border-2 p-3 rounded-lg font-bold text-center transition">Yape</button>
                            <button type="button" @click="form.payment_method = 'plin'" :class="form.payment_method === 'plin' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200'" class="border-2 p-3 rounded-lg font-bold text-center transition">Plin</button>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Total a pagar:</span>
                        <span class="text-2xl font-black text-indigo-600">S/. {{ total }}</span>
                    </div>

                    <div class="flex gap-4">
                        <Link href="/cart" class="flex-1 text-center py-3 text-gray-500 font-bold hover:bg-gray-100 rounded-lg transition">
                            Volver al Carrito
                        </Link>
                        <button type="submit" :disabled="form.processing" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 shadow-md disabled:bg-gray-400">
                            Confirmar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
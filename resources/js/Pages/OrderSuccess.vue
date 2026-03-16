<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    order: Object
});
</script>

<template>
    <Head title="Pedido Exitoso" />
    <AuthenticatedLayout>
        <div class="py-12 px-4">
            <div class="bg-white p-10 rounded-2xl shadow-xl max-w-2xl mx-auto text-center border-t-8 border-green-500">
                <div class="text-6xl mb-4 text-green-500">✅</div>
                <h1 class="text-3xl font-black text-gray-900 mb-2">¡Pedido #{{ order.id }} Recibido!</h1>
                <p class="text-gray-500 mb-8 text-lg">Estamos listos para preparar a tu nuevo compañero.</p>
                
                <div class="bg-indigo-50 p-6 rounded-xl mb-8 border border-indigo-100 text-left">
                    <h3 class="font-bold text-indigo-900 mb-2 flex items-center gap-2">
                        💳 Instrucciones de Pago:
                    </h3>
                    <p class="text-indigo-800">Por favor, realiza el pago de <span class="text-xl font-bold">S/. {{ order.total }}</span> vía <span class="uppercase font-bold">{{ order.payment_method }}</span>.</p>
                    <div class="mt-4 bg-white p-4 rounded-lg border border-indigo-200 text-center">
                        <p class="text-sm text-gray-500 uppercase tracking-widest">Número Yape/Plin</p>
                        <p class="text-3xl font-black text-indigo-600">987 654 321</p>
                    </div>
                    <p class="mt-4 text-sm text-indigo-700 italic text-center">Envía la captura de pantalla por WhatsApp indicando tu número de pedido.</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/orders" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 transition">
                        Ver Mis Pedidos
                    </Link>
                    <Link href="/" class="bg-gray-100 text-gray-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-200 transition">
                        Volver a la Tienda
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
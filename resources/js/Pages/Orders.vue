<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    orders: Array
})
</script>

<template>
    <Head title="Mis Pedidos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mis Pedidos 🐹📦
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <div v-if="orders.length === 0" class="text-center py-4 text-gray-500">
                        No tienes pedidos aún.
                    </div>

                    <div v-for="order in orders" :key="order.id" class="border p-4 mb-6 rounded-lg shadow-sm border-gray-100">

                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-bold text-lg text-indigo-600">Pedido #{{ order.id }}</h2>
                            <span class="text-xs font-bold uppercase bg-gray-200 px-3 py-1 rounded-full text-gray-700">
                                {{ order.status }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-500 mb-4">
                            Fecha: {{ new Date(order.created_at).toLocaleDateString() }}
                        </p>

                        <div class="space-y-3">
                            <div v-for="item in order.items" :key="item.id" class="flex justify-between border-l-4 border-indigo-500 pl-4 py-1 bg-gray-50 rounded-r">
                                <div>
                                    <p class="font-medium text-gray-800">{{ item.guinea_pig.name }}</p>
                                    <p class="text-sm text-gray-500 italic">Cantidad: {{ item.quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-700">S/. {{ item.unit_price }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t flex justify-end">
                            <span class="text-lg font-extrabold text-gray-900">
                                Total: S/. {{ order.total }}
                            </span>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
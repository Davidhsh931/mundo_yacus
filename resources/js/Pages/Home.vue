<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  // Asegúrate de que el controlador envíe 'guineaPigs' con la relación 'seller'
  guineaPigs: Array
});

const page = usePage();

function addToCart(pig) {
    // Si no está logueado, lo mandamos a loguearse antes de comprar
    if (!page.props.auth.user) {
        router.visit('/login');
        return;
    }

    console.log("Intentando agregar al carrito:", pig.name);
    
    // Usamos el ID del animal para la ruta del carrito
    router.post('/cart/add/' + pig.id, {}, {
        onSuccess: () => alert('¡Agregado al carrito de Mundo Yacus! 🐹'),
        onError: (errors) => console.log("Error al agregar:", errors),
    });
}
</script>

<template>
    <Head title="Mercado Directo - Mundo Yacus" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mercado de la Chacra: Sin Intermediarios
            </h2>
        </template>

        <div class="py-12 bg-gray-100">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div v-if="$page.props.flash?.error" class="bg-red-200 text-red-800 p-3 rounded mb-6 border border-red-300">
                    {{ $page.props.flash.error }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div 
                        v-for="pig in guineaPigs" 
                        :key="pig.id"
                        class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow"
                    >
                        <div @click="router.visit('/product/' + pig.id)" class="cursor-pointer">
                            <img
                                v-if="pig.images && pig.images.length"
                                :src="pig.images[0].image_path"
                                class="w-full h-48 object-cover"
                            />
                            <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                Sin foto del producto
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <h2 class="text-xl font-bold text-gray-800">{{ pig.name }}</h2>
                                <span :class="{
                                    'bg-green-100 text-green-800': pig.product_state === 'vivo',
                                    'bg-red-100 text-red-800': pig.product_state === 'beneficiado',
                                    'bg-blue-100 text-blue-800': pig.product_state === 'procesado'
                                }" class="px-2 py-1 rounded text-xs font-bold uppercase">
                                    {{ pig.product_state || 'Vivo' }}
                                </span>
                            </div>

                            <div class="mt-2 space-y-1 min-h-[50px]">
                                <div v-for="(attr, index) in pig.custom_attributes" :key="index" class="text-sm text-gray-600">
                                    <span class="font-semibold">{{ attr.key }}:</span> {{ attr.value }}
                                </div>
                                <p v-if="!pig.custom_attributes" class="text-xs text-gray-400 italic">Sin detalles adicionales</p>
                            </div>

                            <div class="mt-4 flex justify-between items-center border-t pt-4">
                                <div>
                                    <span class="text-2xl font-bold text-indigo-600">S/. {{ pig.price }}</span>
                                    <p class="text-xs text-gray-500">Stock: {{ pig.stock }}</p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">Productor</p>
                                    <p class="text-sm font-bold text-gray-700">{{ pig.seller?.name || 'Habitante Yacus' }}</p>
                                </div>
                            </div>

                            <button
                                @click.stop="addToCart(pig)"
                                :disabled="pig.stock <= 0"
                                class="w-full mt-4 bg-orange-500 text-white py-2 rounded-md font-bold hover:bg-orange-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                            > 
                                {{ pig.stock <= 0 ? 'Agotado' : 'Comprar Directo' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
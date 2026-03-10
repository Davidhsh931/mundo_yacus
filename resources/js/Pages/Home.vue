<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  guineaPigs: Array
});

const page = usePage();

function addToCart(id) {
    // Si no está logueado, lo mandamos a loguearse antes de comprar
    if (!page.props.auth.user) {
        router.visit('/login');
        return;
    }

    console.log("Intentando agregar el cuy id:", id);
    router.post('/cart/add/' + id, {}, {
        onSuccess: () => alert('¡Cuy agregado al carrito! 🐹'),
        onError: (errors) => console.log("Error al agregar:", errors),
    });
}
</script>

<template>
    <Head title="Tienda - Mundo Yacus" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nuestros Cuyes en Venta
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
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 hover:shadow-lg transition-shadow cursor-pointer"
                    >
                        <div @click="router.visit('/product/' + pig.id)">
                            <img
                                v-if="pig.images && pig.images.length"
                                :src="pig.images[0].image_path"
                                class="w-full h-48 object-cover rounded mb-3"
                            />
                            <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center rounded mb-3 text-gray-400">
                                Sin foto
                            </div>

                            <h2 class="text-xl font-bold text-gray-900">{{ pig.name }}</h2>
                            <p class="text-gray-600">{{ pig.breed }}</p>
                            <p class="mt-2 font-bold text-green-600 text-lg">S/ {{ pig.price }}</p>
                            
                            <p :class="pig.stock <= 0 ? 'text-red-500 font-bold' : 'text-gray-500'" class="text-sm mt-1">
                                Stock disponible: {{ pig.stock }}
                            </p>
                        </div>

                        <button
                            @click.stop="addToCart(pig.id)"
                            :disabled="pig.stock <= 0"
                            class="w-full mt-4 bg-indigo-600 text-white px-3 py-2 rounded font-bold hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition"
                        > 
                            {{ pig.stock <= 0 ? 'Agotado' : '🛒 Agregar al carrito' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
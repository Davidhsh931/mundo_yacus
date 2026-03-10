<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductGallery from '../Components/ProductGallery.vue';
import { Head, router } from '@inertiajs/vue3'; // <--- ASEGÚRATE QUE TENGA EL 'router' AQUÍ

const props = defineProps({
    pig: Object
});

// Esta es la función que hace la magia
const addToCart = () => {
    console.log("Agregando al carrito desde detalle, ID:", props.pig.id);
    
    router.post('/cart/add/' + props.pig.id, {}, {
        onSuccess: () => {
            alert('¡' + props.pig.name + ' agregado al carrito! 🐹🛒');
        },
        onError: (err) => {
            console.error("Error al agregar:", err);
        }
    });
};
</script>

<template>
    <Head :title="pig.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ pig.name }} - Detalles
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <ProductGallery :images="pig.images"/>
                        </div>

                        <div>
                            <h1 class="text-4xl font-extrabold text-gray-900">{{ pig.name }}</h1>
                            <p class="text-gray-500 text-lg mt-2 italic">Raza: {{ pig.breed }}</p>
                            <p class="text-3xl font-bold text-green-600 mt-6">S/ {{ pig.price }}</p>

                            <div class="mt-6 border-t pt-4">
                                <p class="text-gray-700 leading-relaxed">{{ pig.description }}</p>
                            </div>

                            <p class="mt-4 font-medium text-gray-900">
                                Stock: <span :class="pig.stock > 0 ? 'text-indigo-600' : 'text-red-600'">{{ pig.stock }}</span>
                            </p>

                            <button 
                                @click="addToCart" 
                                :disabled="pig.stock <= 0"
                                class="mt-8 w-full bg-gray-800 text-white py-4 rounded-md font-bold hover:bg-black transition disabled:bg-gray-400"
                            >
                                {{ pig.stock > 0 ? 'Añadir al Carrito' : 'Agotado' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
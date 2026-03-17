<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductGallery from '../Components/ProductGallery.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    pig: Object // Ahora incluye 'specifications' e 'ia_verification'
});

const addToCart = () => {
    router.post('/cart/add/' + props.pig.id, {}, {
        onSuccess: () => {
            alert('¡' + props.pig.name + ' agregado al carrito! 🐹🛒');
        },
    });
};
</script>

<template>
    <Head :title="pig.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ pig.name }} - Mercado Directo Yacus
                </h2>
                <span class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-xs font-black uppercase">
                    {{ pig.species || 'Cuy' }}
                </span>
            </div>
        </template>

        <div class="py-12 bg-gray-50">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-8 border border-gray-100">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div>
                            <ProductGallery :images="pig.images"/>
                            
                            <div v-if="pig.ia_verification" class="mt-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl border border-green-200 flex items-center gap-4 shadow-sm">
                                <div class="text-3xl">🤖</div>
                                <div>
                                    <h4 class="text-green-900 font-bold text-sm uppercase tracking-tight">Sello de Calidad IA Mundo Yacus</h4>
                                    <p class="text-green-700 text-xs">
                                        Validado como: <span class="font-bold">{{ pig.ia_verification.raza_detectada }}</span> 
                                        con un <span class="font-bold">{{ pig.ia_verification.confianza }}</span> de precisión.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h1 class="text-4xl font-black text-gray-900 leading-none">{{ pig.name }}</h1>
                                    <p class="text-indigo-500 font-medium mt-2">Origen: Chacras de Yacus, Perú</p>
                                </div>
                                <span :class="{
                                    'bg-green-100 text-green-800 border-green-200': pig.product_state === 'vivo',
                                    'bg-amber-100 text-amber-800 border-amber-200': pig.product_state === 'beneficiado',
                                    'bg-blue-100 text-blue-800 border-blue-200': pig.product_state === 'procesado'
                                }" class="px-4 py-1.5 rounded-full text-xs font-black uppercase border shadow-sm">
                                    {{ pig.product_state }}
                                </span>
                            </div>

                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-gray-900">S/ {{ pig.price }}</span>
                                <span class="text-gray-400 text-sm">/ unidad</span>
                            </div>

                            <div v-if="pig.specifications && pig.specifications.length" class="bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-inner">
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center">
                                    <span class="mr-2">📋</span> Ficha Técnica de la Chacra
                                </h3>
                                <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                                    <div v-for="(attr, index) in pig.specifications" :key="index" class="border-b border-gray-200 pb-2">
                                        <p class="text-[10px] text-gray-500 uppercase font-bold">{{ attr.key }}</p>
                                        <p class="font-bold text-gray-800">{{ attr.value }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 p-5 bg-white rounded-2xl border-2 border-orange-100 shadow-sm">
                                <div class="bg-orange-500 text-white h-14 w-14 rounded-2xl flex items-center justify-center font-black text-2xl shadow-md rotate-3">
                                    {{ pig.seller?.name?.charAt(0) || 'H' }}
                                </div>
                                <div>
                                    <p class="text-[10px] text-orange-600 font-black uppercase tracking-widest">Vendedor Directo</p>
                                    <p class="text-xl font-black text-gray-900">{{ pig.seller?.name || 'Habitante de Yacus' }}</p>
                                    <p class="text-xs text-gray-500">Miembro verificado de la Cooperativa</p>
                                </div>
                            </div>

                            <div class="pt-4">
                                <h4 class="text-sm font-bold text-gray-900 mb-2 underline decoration-indigo-300">Descripción del Producto</h4>
                                <p class="text-gray-600 leading-relaxed text-sm">{{ pig.description || 'Sin descripción adicional.' }}</p>
                            </div>

                            <div class="pt-6">
                                <div class="flex items-center justify-between mb-4">
                                    <p class="text-sm font-bold text-gray-900">Stock en Chacra:</p>
                                    <span class="text-sm font-black text-green-600 bg-green-50 px-3 py-1 rounded-lg">
                                        {{ pig.stock || 5 }} unidades disponibles
                                    </span>
                                </div>
                                <button 
                                    @click="addToCart" 
                                    class="w-full bg-gray-900 text-white py-5 rounded-2xl font-black text-lg hover:bg-indigo-700 transition-all transform hover:-translate-y-1 active:scale-95 shadow-xl flex items-center justify-center gap-3"
                                >
                                    <span>🛒</span> Añadir al Carrito Directo
                                </button>
                                <p class="text-center text-[10px] text-gray-400 mt-4 uppercase font-bold tracking-widest">
                                    Compra protegida por el sello de confianza Mundo Yacus
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
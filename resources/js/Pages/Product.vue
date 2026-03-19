<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductGallery from '../Components/ProductGallery.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    pig: Object
});

// COMPRENSION DE IMÁGENES BLINDADA
const FALLBACK_IMAGE = '/images/cobaya-fondo-blanco.jpg';
const isInvalidImageValue = (value) => {
    if (value === null || value === undefined) return true;
    if (value === 0 || value === '0') return true;
    if (typeof value === 'string' && value.trim() === '') return true;
    return false;
};

const formattedImages = computed(() => {
    // Si no hay imágenes, creamos un objeto de respaldo para evitar errores en el carrusel
    if (!props.pig.images || props.pig.images.length === 0) {
        return [{
            id: 'placeholder',
            image_path: FALLBACK_IMAGE
        }];
    }

    return props.pig.images.map(img => {
        let path = img.image_path;
        if (isInvalidImageValue(path)) path = FALLBACK_IMAGE;
        else if (typeof path === 'string' && path.startsWith('http')) path = path;
        else if (typeof path === 'string') path = path.startsWith('/storage/') ? path : '/storage/' + path.replace(/^\/?storage\/?/, '');
        else path = FALLBACK_IMAGE;
        return { ...img, image_path: path };
    });
});

const addToCart = () => {
    if (props.pig.stock <= 0) return;
    router.post('/cart/add/' + props.pig.id, {}, {
        onSuccess: () => alert('¡' + props.pig.name + ' ya está en tu carrito! 🐹🛒'),
    });
};
</script>

<template>
    <Head :title="pig.name + ' - Mundo Yacus'" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center max-w-7xl mx-auto">
                <nav class="flex text-gray-500 text-xs font-bold uppercase tracking-widest">
                    <Link href="/" class="hover:text-indigo-600">Mercado</Link>
                    <span class="mx-2">/</span>
                    <span class="text-indigo-600">{{ pig.name }}</span>
                </nav>
            </div>
        </template>

        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 overflow-hidden border border-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        
                        <div class="p-8 lg:p-12 bg-gray-50/50">
                            <ProductGallery :images="formattedImages" />
                            
                            <div v-if="pig.ia_verification" 
                                 class="mt-8 p-6 bg-white rounded-3xl border-2 border-green-100 flex items-center gap-6 shadow-sm relative overflow-hidden">
                                <div class="absolute top-0 right-0 bg-green-500 text-white text-[8px] font-black px-4 py-1 rounded-bl-xl uppercase tracking-tighter">Verified AI</div>
                                <div class="text-4xl animate-bounce">🤖</div>
                                <div>
                                    <h4 class="text-green-900 font-black text-sm uppercase">Sello de Calidad Mundo Yacus</h4>
                                    <p class="text-green-700 text-xs font-medium leading-relaxed">
                                        Análisis biomecánico: <span class="font-bold">Aprobado</span>. 
                                        Precisión del modelo: <span class="font-black text-green-900">{{ pig.ia_verification.confianza }}</span>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 lg:p-12 space-y-8">
                            <div>
                                <span class="bg-indigo-100 text-indigo-700 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border border-indigo-200">
                                    {{ pig.species || 'Cuy de Raza' }}
                                </span>
                                <h1 class="text-5xl font-black text-gray-900 mt-4 tracking-tight">{{ pig.name }}</h1>
                                <p class="text-indigo-500 font-bold flex items-center mt-2">
                                    <span class="mr-2 text-lg">📍</span> Chacras de Yacus, Perú
                                </p>
                            </div>

                            <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-3xl border border-gray-100 inline-flex">
                                <span class="text-4xl font-black text-gray-900">S/ {{ pig.price }}</span>
                                <div class="h-8 w-[1px] bg-gray-300"></div>
                                <span class="text-gray-500 font-bold text-sm">IVA incluido</span>
                            </div>

                            <div v-if="pig.specifications?.length" class="space-y-4">
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] flex items-center">
                                    📋 Especificaciones Técnicas
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div v-for="(attr, index) in pig.specifications" :key="index" 
                                         class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                        <p class="text-[9px] text-gray-400 uppercase font-black tracking-tighter mb-1">{{ attr.key }}</p>
                                        <p class="font-bold text-gray-800 text-sm">{{ attr.value }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 p-6 bg-gradient-to-br from-orange-50 to-white rounded-[2rem] border-2 border-orange-100">
                                <div class="bg-orange-500 text-white h-14 w-14 rounded-2xl flex items-center justify-center font-black text-2xl shadow-lg shadow-orange-200">
                                    {{ pig.seller?.name?.charAt(0) || 'Y' }}
                                </div>
                                <div>
                                    <p class="text-[9px] text-orange-600 font-black uppercase tracking-widest">Productor Directo</p>
                                    <p class="text-xl font-black text-gray-900">{{ pig.seller?.name || 'Habitante de Yacus' }}</p>
                                    <div class="flex items-center text-xs text-gray-500 font-bold mt-1">
                                        <span class="text-green-500 mr-1">✓</span> Identidad Verificada
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <div class="flex items-center justify-between mb-4 px-2">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Stock Disponible:</p>
                                    <span :class="pig.stock > 0 ? 'text-green-600' : 'text-red-500'" class="font-black text-sm">
                                        {{ pig.stock }} unidades
                                    </span>
                                </div>

                                <button @click="addToCart" 
                                        :disabled="pig.stock <= 0"
                                        class="w-full group relative flex items-center justify-center gap-3 bg-gray-900 text-white py-6 rounded-[2rem] font-black text-xl transition-all duration-300 hover:bg-indigo-700 disabled:bg-gray-300 active:scale-95 shadow-2xl shadow-indigo-200">
                                    <span v-if="pig.stock > 0" class="flex items-center gap-3">
                                        🛒 Añadir al Carrito
                                    </span>
                                    <span v-else>🚫 Agotado</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
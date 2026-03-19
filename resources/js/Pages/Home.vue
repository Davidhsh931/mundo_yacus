<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
  guineaPigs: Array
});

const page = usePage();

// "Cerebro" de imágenes mejorado: Valida longitud para evitar el error /0
const FALLBACK_IMAGE = '/images/cobaya-fondo-blanco.jpg';

const isInvalidImageValue = (value) => {
    if (value === null || value === undefined) return true;
    if (value === 0 || value === '0') return true;
    if (typeof value === 'string' && value.trim() === '') return true;
    return false;
};

const getProductImage = (pig) => {
    if (!pig?.images?.length) return FALLBACK_IMAGE;

    const path = pig.images[0]?.image_path;
    if (isInvalidImageValue(path)) return FALLBACK_IMAGE;
    if (path.startsWith('http')) return path;

    // Normaliza la ruta: asegura que siempre empiece con /storage/
    let fixedPath = path.startsWith('/') ? path : '/' + path;
    if (!fixedPath.startsWith('/storage/')) {
        // Evita "storage/storage/"
        fixedPath = '/storage/' + fixedPath.replace(/^\/?storage\/?/, '');
    }

    return fixedPath;
};

const getSafeProductImageSrc = (pig) => {
    const src = getProductImage(pig);
    return isInvalidImageValue(src) ? null : src;
};

function addToCart(pig) {
    if (!page.props.auth.user) {
        router.visit('/login');
        return;
    }
    router.post('/cart/add/' + pig.id, {}, {
        onSuccess: () => alert('¡Agregado al carrito de Mundo Yacus! 🐹🛒'),
    });
}
</script>

<template>
    <Head title="Mercado Directo - Mundo Yacus" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                🌾 Mercado de la Chacra: <span class="text-indigo-600">Sin Intermediarios</span>
            </h2>
        </template>

        <div class="py-12 bg-gray-50">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                    <div v-for="pig in guineaPigs" :key="pig.id" 
                         class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                        
                        <div @click="router.visit('/product/' + pig.id)" class="relative cursor-pointer overflow-hidden aspect-video">
                            <template v-if="pig.images && pig.images.length > 0">
                                <img :src="getSafeProductImageSrc(pig)" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500"
                                     @error="(e) => { e.target.removeAttribute('src'); e.target.src = FALLBACK_IMAGE; }" />
                            </template>
                            <div v-else class="w-full h-full bg-gray-200 flex flex-col items-center justify-center text-gray-400">
                                <span class="text-4xl mb-2">📸</span>
                                <p class="text-[10px] font-black uppercase tracking-widest text-center">Sin registro visual</p>
                            </div>
                            
                            <div class="absolute top-4 right-4">
                                <span :class="pig.product_state === 'vivo' ? 'bg-green-500' : 'bg-orange-500'" 
                                      class="text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-lg">
                                    {{ pig.product_state || 'Vivo' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h2 class="text-xl font-black text-gray-900 mb-1 capitalize">{{ pig.name }}</h2>
                            <p class="text-xs text-gray-500 mb-4 font-medium italic">Producido por {{ pig.seller?.name || 'Habitante Yacus' }}</p>

                            <div class="space-y-2 mb-6 min-h-[60px]">
                                <div v-for="(attr, index) in pig.specifications?.slice(0, 2)" :key="index" 
                                     class="flex justify-between text-sm border-b border-gray-50 pb-1">
                                    <span class="text-gray-400 font-bold uppercase text-[10px]">{{ attr.key }}:</span>
                                    <span class="text-gray-700 font-medium">{{ attr.value }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-auto">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-black uppercase leading-none">Precio</p>
                                    <span class="text-2xl font-black text-indigo-600">S/ {{ pig.price }}</span>
                                </div>
                                <button @click.stop="addToCart(pig)" 
                                        :disabled="pig.stock <= 0" 
                                        class="bg-gray-900 text-white px-6 py-3 rounded-2xl font-black text-xs hover:bg-indigo-600 disabled:bg-gray-200 transition-colors shadow-lg active:scale-95">
                                    {{ pig.stock <= 0 ? 'AGOTADO' : 'COMPRAR' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
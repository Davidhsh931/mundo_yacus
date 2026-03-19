<template>
  <div class="w-full select-none" v-if="images && images.length">

    <swiper
      :modules="modules"
      :space-between="10"
      :navigation="true"
      :thumbs="{ swiper: thumbsSwiper }"
      class="main-swiper rounded-[2rem] shadow-2xl mb-6 relative overflow-hidden bg-white border border-gray-100"
      @slideChange="onSlideChange"
    >
      <swiper-slide v-for="(img, index) in images" :key="img.id || index">
        <div class="relative h-[450px] w-full flex items-center justify-center bg-gray-50 overflow-hidden group">
          
          <img
            :src="formatPath(img.image_path)"
            class="w-full h-full object-cover cursor-zoom-in transition-opacity duration-500"
            @mousemove="handleZoom($event, index)"
            @mouseleave="hideZoom(index)"
            @error="(e) => { e.target.removeAttribute('src'); e.target.src = FALLBACK_IMAGE; }"
          />

          <div
            v-if="zoomVisible[index] && images[index]"
            :style="zoomStyle[index]"
            class="absolute border-2 border-white/80 rounded-full pointer-events-none z-50 shadow-[0_0_40px_rgba(0,0,0,0.3)] backdrop-blur-[2px]"
          ></div>

          <div class="absolute bottom-4 right-4 bg-black/20 backdrop-blur-md text-white text-[10px] px-3 py-1 rounded-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none font-bold uppercase tracking-widest">
            Zoom Activo
          </div>
        </div>
      </swiper-slide>
    </swiper>

    <swiper
      @swiper="setThumbsSwiper"
      :space-between="14"
      :slides-per-view="4"
      :free-mode="true"
      :watch-slides-progress="true"
      :modules="modules"
      class="thumb-swiper px-2"
    >
      <swiper-slide
        v-for="(img, index) in images"
        :key="'thumb-' + (img.id || index)"
        class="cursor-pointer py-2"
      >
        <div
          class="aspect-square rounded-2xl overflow-hidden border-2 transition-all duration-300 transform"
          :class="selectedIndex === index 
            ? 'border-indigo-600 scale-110 shadow-lg shadow-indigo-100' 
            : 'border-transparent opacity-50 hover:opacity-100'"
          @click="goToSlide(index)"
        >
          <img 
            :src="formatPath(img.image_path)" 
            class="w-full h-full object-cover" 
            @error="(e) => { e.target.removeAttribute('src'); e.target.src = FALLBACK_IMAGE; }"
          />
        </div>
      </swiper-slide>
    </swiper>

  </div>

  <div v-else class="h-[400px] flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-gray-100 p-12 text-center shadow-inner">
    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-4 animate-pulse">
      📷
    </div>
    <h3 class="text-gray-900 font-black uppercase text-sm tracking-widest">Sin registro visual</h3>
    <p class="text-gray-400 text-xs mt-2 max-w-[200px] leading-relaxed">
      Este ejemplar aún no cuenta con fotografías en la galería de Mundo Yacus.
    </p>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Thumbs, Zoom, FreeMode } from 'swiper/modules';

// Estilos Swiper
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import 'swiper/css/free-mode';

const props = defineProps({ images: Array });
const modules = [Navigation, Thumbs, Zoom, FreeMode];

const FALLBACK_IMAGE = '/images/cobaya-fondo-blanco.jpg';
const isInvalidImageValue = (value) => {
  if (value === null || value === undefined) return true;
  if (value === 0 || value === '0') return true;
  if (typeof value === 'string' && value.trim() === '') return true;
  return false;
};

const thumbsSwiper = ref(null);
const setThumbsSwiper = (swiper) => (thumbsSwiper.value = swiper);

const selectedIndex = ref(0);
const zoomVisible = reactive({});
const zoomStyle = reactive({});

// Formateador inteligente de rutas (Previene duplicados de /storage/)
const formatPath = (path) => {
  if (isInvalidImageValue(path)) return FALLBACK_IMAGE;
  if (typeof path === 'string' && path.startsWith('http')) return path;
  const str = typeof path === 'string' ? path : String(path);
  if (str.startsWith('/storage/')) return str;
  return '/storage/' + str.replace(/^\/?storage\/?/, '');
};

function goToSlide(index) {
  selectedIndex.value = index;
  if (thumbsSwiper.value) {
    thumbsSwiper.value.slideTo(index);
  }
}

function onSlideChange(swiper) {
  selectedIndex.value = swiper.activeIndex;
}

// Lógica de Lupa con protección contra errores /0
function handleZoom(e, index) {
  if (!props.images || !props.images[index]) return;

  const rect = e.target.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;
  
  const fullPath = formatPath(props.images[index].image_path);
  const zoomFactor = 2.5; // Nivel de aumento

  zoomVisible[index] = true;
  zoomStyle[index] = {
    top: `${y - 75}px`,
    left: `${x - 75}px`,
    width: `150px`,
    height: `150px`,
    backgroundImage: `url(${fullPath})`, 
    backgroundSize: `${rect.width * zoomFactor}px ${rect.height * zoomFactor}px`,
    backgroundPosition: `-${x * zoomFactor - 75}px -${y * zoomFactor - 75}px`,
  };
}

function hideZoom(index) {
  zoomVisible[index] = false;
}
</script>

<style scoped>
.main-swiper {
  --swiper-navigation-color: #4f46e5;
  --swiper-navigation-size: 20px;
}

/* Personalización de botones de navegación */
:deep(.swiper-button-next), 
:deep(.swiper-button-prev) {
  background: white;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  border: 1px solid #f3f4f6;
  transition: all 0.3s ease;
}

:deep(.swiper-button-next:after), 
:deep(.swiper-button-prev:after) {
  font-weight: 900;
}

:deep(.swiper-button-next:hover), 
:deep(.swiper-button-prev:hover) {
  background: #4f46e5;
  color: white;
  transform: scale(1.1);
}

.thumb-swiper .swiper-slide img {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
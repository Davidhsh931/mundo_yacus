<template>
  <div class="w-full" v-if="images && images.length">

    <!-- Slider principal con Zoom estilo Amazon -->
    <swiper
      :modules="modules"
      :space-between="10"
      :navigation="true"
      :thumbs="{ swiper: thumbsSwiper }"
      class="main-swiper rounded-lg shadow-lg mb-4 relative"
      @slideChange="onSlideChange"
    >
      <swiper-slide v-for="(img,index) in images" :key="img.id">
        <div class="swiper-zoom-container bg-gray-100 h-96 overflow-hidden relative">
          <img
            :src="img.image_path"
            :data-preload="true"
            class="w-full h-full object-cover cursor-zoom-in transition-transform duration-300"
            @mousemove="moveZoom($event,index)"
            @mouseleave="resetZoom(index)"
            ref="mainImages"
          />
          <!-- Lupa flotante -->
          <div
            v-if="zoomVisible[index]"
            :style="zoomStyle[index]"
            class="absolute border border-gray-400 rounded-lg pointer-events-none"
          ></div>
        </div>
      </swiper-slide>
    </swiper>

    <!-- Thumbnails -->
    <swiper
      @swiper="setThumbsSwiper"
      :space-between="10"
      :slides-per-view="4"
      :free-mode="true"
      :watch-slides-progress="true"
      :modules="modules"
      class="thumb-swiper"
    >
      <swiper-slide
        v-for="(img,index) in images"
        :key="'thumb-'+img.id"
        class="cursor-pointer"
        @click="goToSlide(index)"
      >
        <div
          class="border-2 border-transparent hover:border-orange-500 rounded overflow-hidden transition-all duration-300"
          :class="{'border-orange-500 scale-105': selectedIndex === index}"
        >
          <img :src="img.image_path" class="w-full h-20 object-cover transition-transform duration-300" />
        </div>
      </swiper-slide>
    </swiper>

  </div>

  <div v-else class="text-gray-400 text-center p-10 bg-gray-100 rounded border-2 border-dashed">
    <p>📷 Sin fotos de este cuy todavía</p>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Thumbs, Zoom, FreeMode } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';
import 'swiper/css/zoom';
import 'swiper/css/free-mode';

const props = defineProps({ images: Array });
const modules = [Navigation, Thumbs, Zoom, FreeMode];

// Swiper Thumbs
const thumbsSwiper = ref(null);
const setThumbsSwiper = (swiper) => thumbsSwiper.value = swiper;

// Slide activo
const selectedIndex = ref(0);
const mainImages = ref([]);
const zoomVisible = reactive({});
const zoomStyle = reactive({});

// Función para ir a slide desde miniatura
function goToSlide(index){
  selectedIndex.value = index;
  if(thumbsSwiper.value){
    thumbsSwiper.value.slideTo(index);
  }
}

// Evento de cambio de slide
function onSlideChange(swiper){
  selectedIndex.value = swiper.activeIndex;
}

// Zoom con lupa flotante
function moveZoom(e,index){
  const rect = e.target.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;
  zoomVisible[index] = true;
  zoomStyle[index] = {
    top: `${y - 50}px`,
    left: `${x - 50}px`,
    width: `100px`,
    height: `100px`,
    background: `url(${props.images[index].image_path})`,
    backgroundSize: `${rect.width * 2}px ${rect.height * 2}px`,
    backgroundPosition: `-${x * 2 - 50}px -${y * 2 - 50}px`,
  };
}

function resetZoom(index){
  zoomVisible[index] = false;
}
</script>

<style scoped>
.main-swiper {
  height: 400px;
  --swiper-navigation-color: #f97316;
}

.swiper-slide-thumb-active div {
  border-color: #f97316 !important;
}

.thumb-swiper .swiper-slide img {
  transition: transform 0.3s;
}

.thumb-swiper .swiper-slide.scale-105 img {
  transform: scale(1.05);
}
</style>
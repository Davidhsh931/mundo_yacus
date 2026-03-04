<!-- resources/js/Pages/App.vue -->
<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Cabecera común -->
    <header class="bg-green-600 text-white p-4">
      <h1 class="text-2xl font-bold">🐹 Mundo Yacus</h1>
    </header>

    <!-- Slot para la página específica -->
    <main class="p-6">
      <slot></slot>
    </main>

    <!-- Footer común -->
    <footer class="bg-gray-200 text-gray-700 text-center p-4 mt-6">
      © 2026 Mundo Yacus
    </footer>
  </div>
</template>

<script setup>
// No props necesarios aquí
</script>
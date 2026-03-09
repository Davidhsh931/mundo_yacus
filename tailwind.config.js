/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue", // <--- ¡ESTA LÍNEA ES LA MÁS IMPORTANTE!
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
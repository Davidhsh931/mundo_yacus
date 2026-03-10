<form 
  :action="`/admin/guinea-pigs/${pig.id}/upload-image`" 
  method="POST" 
  enctype="multipart/form-data"
  class="mt-6 border p-4 rounded bg-gray-100"
>
  <input type="hidden" name="_token" :value="$page.props.csrf_token">

  <label class="block font-bold mb-2">
    Subir nueva imagen
  </label>

  <input 
    type="file" 
    name="image" 
    class="mb-4"
  />

  <button 
    type="submit"
    class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600"
  >
    Subir imagen
  </button>
</form>
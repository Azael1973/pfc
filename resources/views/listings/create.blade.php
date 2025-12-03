<x-app-layout>
<div class="py-6">
  <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Publicar anuncio</h1>

      <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Título</label>
          <input name="title" value="{{ old('title') }}" required maxlength="200" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
          @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Categoría</label>
            <select name="category_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">
              <option value="">Sin categoría</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
            @error('category_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Precio (€)</label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
            @error('price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Estado</label>
            <select name="condition" required class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">
              @foreach (['new'=>'Nuevo','like_new'=>'Como nuevo','used'=>'Usado','for_parts'=>'Para piezas'] as $k=>$v)
                <option value="{{ $k }}" @selected(old('condition')===$k)>{{ $v }}</option>
              @endforeach
            </select>
            @error('condition') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Ciudad</label>
          <input name="city" value="{{ old('city') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
          @error('city') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Descripción</label>
          <textarea name="description" rows="5" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">{{ old('description') }}</textarea>
          @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Imágenes (obligatorio, puedes seleccionar varias)</label>
          <input type="file" name="images[]" multiple required accept="image/*" data-accumulate-files data-max-files="10" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100" />
          @error('images') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          @error('images.*') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          <p class="text-xs text-gray-500 mt-1">Formatos permitidos: jpg, jpeg, png, webp. Máx 5MB c/u. Hasta 10.</p>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ route('listings.index') }}" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700">Cancelar</a>
          <button class="px-4 py-2 rounded bg-emerald-600 text-white">Publicar</button>
        </div>
      </form>
    </div>
  </div>
</div>
</x-app-layout>

<script>
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("[data-accumulate-files]").forEach(input => {
    const limit = parseInt(input.dataset.maxFiles || "10", 10);
    const dt = new DataTransfer();
    input.addEventListener("change", () => {
      for (const file of Array.from(input.files)) {
        if (dt.files.length >= limit) break;
        dt.items.add(file);
      }
      input.files = dt.files;
      if (dt.files.length >= limit) {
        alert("Solo puedes seleccionar hasta " + limit + " imagenes en total.");
      }
    });
  });
});
</script>

<x-app-layout>
<div class="py-6">
  <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Editar anuncio</h1>

      <form method="POST" action="{{ route('listings.update', $listing) }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Título</label>
          <input name="title" value="{{ old('title', $listing->title) }}" required maxlength="200" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
          @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="grid md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Categoría</label>
            <select name="category_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">
              <option value="">Sin categoría</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(old('category_id', $listing->category_id)==$cat->id)>{{ $cat->name }}</option>
              @endforeach
            </select>
            @error('category_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Precio (€)</label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $listing->price) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
            @error('price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Condición</label>
            <select name="condition" required class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">
              @foreach (['new'=>'Nuevo','like_new'=>'Como nuevo','used'=>'Usado','for_parts'=>'Para piezas'] as $k=>$v)
                <option value="{{ $k }}" @selected(old('condition', $listing->condition)===$k)>{{ $v }}</option>
              @endforeach
            </select>
            @error('condition') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Estado</label>
            <select name="status" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">
              @foreach (['available' => 'Disponible','reserved'=>'Reservado','sold'=>'Vendido'] as $k=>$v)
                <option value="{{ $k }}" @selected(old('status', $listing->status)===$k)>{{ $v }}</option>
              @endforeach
            </select>
            @error('status') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Ciudad</label>
            <input name="city" value="{{ old('city', $listing->city) }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
            @error('city') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-300">Descripción</label>
          <textarea name="description" rows="5" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">{{ old('description', $listing->description) }}</textarea>
          @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-3 text-sm text-gray-600 dark:text-gray-300">
          Gestiona las imágenes desde la galería del anuncio (subir/ordenar/eliminar).
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ route('listings.show', $listing) }}" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700">Cancelar</a>
          <button class="px-4 py-2 rounded bg-emerald-600 text-white">Guardar cambios</button>
        </div>
      </form>

      <div class="mt-10">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Subir imágenes</h2>
        <form method="POST" action="{{ route('listings.images.store', $listing) }}" enctype="multipart/form-data" class="space-y-3">
          @csrf
          <input type="file" name="images[]" multiple accept="image/*" data-accumulate-files data-max-files="10" class="block w-full text-sm text-gray-900 dark:text-gray-100" required>
          <p class="text-xs text-gray-500 dark:text-gray-400">Puedes seleccionar varias a la vez (hasta 10 por carga). Si ya hay imágenes, no se reemplazan, se añadirán nuevas.</p>
          @error('images') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          @error('images.*') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
          <button class="px-4 py-2 rounded bg-emerald-600 text-white">Subir imágenes</button>
        </form>
      </div>
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

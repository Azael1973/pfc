<x-app-layout>
<div class="py-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <div class="relative aspect-square bg-gray-100 dark:bg-gray-700 rounded overflow-hidden">
            @php $first = $listing->images->first() ?? null; @endphp
            @if($first && ($first->path || $first->thumb_path))
              <img id="mainImage" src="{{ asset('storage/'.($first->path ?? $first->thumb_path)) }}" class="w-full h-full object-cover" />
              @if($listing->images->count() > 1)
                <button type="button" data-gallery-prev class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-900/80 rounded-full w-9 h-9 flex items-center justify-center shadow">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </button>
                <button type="button" data-gallery-next class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-900/80 rounded-full w-9 h-9 flex items-center justify-center shadow">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </button>
              @endif
            @else
              <div class="w-full h-full flex items-center justify-center text-gray-400">Sin imagen</div>
            @endif
          </div>
          @if($listing->images->count() > 1)
            <div class="mt-3 grid grid-cols-5 gap-2">
              @foreach($listing->images as $img)
                @php $src = asset('storage/'.($img->thumb_path ?? $img->path)); @endphp
                <img src="{{ $src }}" data-gallery-image data-full="{{ asset('storage/'.($img->path ?? $img->thumb_path ?? '')) }}" class="aspect-square w-full object-cover rounded cursor-pointer border" />
              @endforeach
            </div>
          @endif

          
        </div>

        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $listing->title }}</h1>
          <div class="text-3xl text-emerald-600 font-bold mt-1">{{ $listing->price ? number_format($listing->price,2) . ' €' : '—' }}</div>
          @php
            $condMap = [
              'new' => 'Nuevo',
              'like_new' => 'Como nuevo',
              'used' => 'Usado',
              'for_parts' => 'Para piezas',
            ];
            $statusMap = [
              'available' => 'Disponible',
              'reserved' => 'Reservado',
              'sold' => 'Vendido',
            ];
            $condLabel = $condMap[$listing->condition] ?? ucfirst(str_replace('_',' ', $listing->condition));
            $statusLabel = $statusMap[$listing->status] ?? ucfirst($listing->status);
          @endphp
          <div class="mt-2 flex gap-2 items-center text-sm">
            <span class="px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700">{{ $condLabel }}</span>
            <span class="px-2 py-0.5 rounded {{ $listing->status==='available' ? 'bg-emerald-100 text-emerald-700' : ($listing->status==='reserved' ? 'bg-amber-100 text-amber-700' : 'bg-gray-200 text-gray-700') }}">{{ $statusLabel }}</span>
            <span class="text-gray-500">{{ $listing->city ?? '—' }}</span>
          </div>

          @if($listing->category)
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">Categoría: {{ $listing->category->name }}</div>
          @endif

          <p class="mt-4 text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $listing->description }}</p>

          <div class="mt-6 text-sm text-gray-500">Vendedor: {{ $listing->user->name }}</div>

          @auth
            @if(auth()->id() === $listing->user_id)
              <div class="mt-4">
                <a href="{{ route('listings.edit', $listing) }}" class="inline-flex items-center px-4 py-2 rounded bg-blue-600 text-white mb-3">Editar anuncio</a>
                <form method="POST" action="{{ route('listings.destroy', $listing) }}" onsubmit="return confirm('¿Eliminar este anuncio? Esta acción no se puede deshacer.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white">Eliminar anuncio</button>
                </form>
              </div>
            @endif
          @endauth

          @auth
            @if(auth()->id() !== $listing->user_id)
              <div class="mt-6 space-y-4">
                @if(!is_null($listing->price) && $listing->price > 0)
                  <a href="{{ route('listings.checkout', $listing) }}"
                     class="flex items-center justify-center px-4 py-3 rounded bg-emerald-600 text-white text-lg font-semibold">
                    Comprar ahora por {{ number_format($listing->price, 2) }} €
                  </a>
                @endif
                @php $fav = !empty($isFavorited); @endphp
                <button type="button"
                        data-favorite-toggle
                        data-listing-id="{{ $listing->id }}"
                        data-favorited="{{ $fav ? '1' : '0' }}"
                        data-url-store="{{ route('favorites.store', $listing) }}"
                        data-url-destroy="{{ route('favorites.destroy', $listing) }}"
                        class="px-4 py-2 rounded border flex items-center gap-2 {{ $fav ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'bg-pink-600 text-white' }}"
                        title="{{ $fav ? 'Quitar de favoritos' : 'Añadir a favoritos' }}">
                  <svg data-fav-filled xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 {{ $fav ? '' : 'hidden' }}">
                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003a.75.75 0 01-.665 0z"/>
                  </svg>
                  <svg data-fav-outline xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5 {{ $fav ? 'hidden' : '' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75z"/>
                  </svg>
                  <span data-fav-label>{{ $fav ? 'Quitar de favoritos' : 'Añadir a favoritos' }}</span>
                </button>

                <form method="POST" action="{{ route('offers.store', $listing) }}" class="space-y-2">
                  @csrf
                  <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">Tu oferta (€)</label>
                    <input name="amount" type="number" step="0.01" min="1" value="{{ $listing->price ?? '' }}" class="mt-1 rounded w-48 border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
                  </div>
                  <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">Mensaje (opcional)</label>
                    <textarea name="message" rows="2" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700"></textarea>
                  </div>
                  <button class="px-4 py-2 bg-emerald-600 text-white rounded">Enviar oferta</button>
                </form>

                <form method="POST" action="{{ route('conversations.store') }}">
                  @csrf
                  <input type="hidden" name="listing_id" value="{{ $listing->id }}" />
                  <button class="px-4 py-2 bg-blue-600 text-white rounded">Chat con vendedor</button>
                </form>
              </div>
            @endif
          @endauth
        </div>
      </div>
    </div>
  </div>
</div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const main = document.getElementById('mainImage');
  const thumbs = Array.from(document.querySelectorAll('[data-gallery-image]'));
  const imgs = thumbs.map(t => t.getAttribute('data-full') || t.src).filter(Boolean);
  let idx = 0;
  const update = () => {
    if (main && imgs.length) {
      main.src = imgs[idx];
    }
  };
  thumbs.forEach((t, i) => {
    t.addEventListener('click', () => {
      idx = i;
      update();
    });
  });
  const prev = document.querySelector('[data-gallery-prev]');
  const next = document.querySelector('[data-gallery-next]');
  if (prev) prev.addEventListener('click', (e) => {
    e.preventDefault();
    if (!imgs.length) return;
    idx = (idx + imgs.length - 1) % imgs.length;
    update();
  });
  if (next) next.addEventListener('click', (e) => {
    e.preventDefault();
    if (!imgs.length) return;
    idx = (idx + 1) % imgs.length;
    update();
  });
});
</script>

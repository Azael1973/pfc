<x-app-layout>
<div class="py-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Mis favoritos</h1>

      <div id="favorites-grid" data-favorites-grid class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($favorites as $listing)
          @php $first = $listing->images->first() ?? null; @endphp
          <div class="relative border rounded-md overflow-hidden" data-listing-card data-listing-id="{{ $listing->id }}">
            <a href="{{ route('listings.show',$listing) }}" class="block">
              <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                @if($first && ($first->thumb_path || $first->path))
                  <img src="{{ asset('storage/'.($first->thumb_path ?? $first->path)) }}" class="w-full h-full object-cover" />
                @else
                  <span class="text-gray-400">Sin imagen</span>
                @endif
              </div>
            </a>
            <div class="p-3">
              <div class="text-gray-900 dark:text-gray-100 font-medium truncate">{{ $listing->title }}</div>
              <div class="text-emerald-600 font-semibold">{{ $listing->price ? number_format($listing->price,2).' €' : '—' }}</div>
              <div class="text-sm text-gray-500">{{ $listing->city ?? '—' }}</div>
            </div>

            <div class="absolute top-2 right-2">
              <button type="button"
                      data-favorite-toggle
                      data-listing-id="{{ $listing->id }}"
                      data-favorited="1"
                      data-url-store="{{ route('favorites.store', $listing) }}"
                      data-url-destroy="{{ route('favorites.destroy', $listing) }}"
                      class="w-9 h-9 rounded-full bg-white/90 dark:bg-gray-800/90 border flex items-center justify-center hover:scale-105 transition"
                      title="Quitar de favoritos">
                <svg data-fav-filled xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-pink-600">
                  <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003a.75.75 0 01-.665 0z"/>
                </svg>
                <svg data-fav-outline xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5 text-pink-600 hidden">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75z"/>
                </svg>
              </button>
            </div>
          </div>
        @empty
          <p class="text-gray-500">Aún no tienes favoritos.</p>
        @endforelse
      </div>

      <div class="mt-6">{{ $favorites->links() }}</div>
    </div>
  </div>
</div>
</x-app-layout>

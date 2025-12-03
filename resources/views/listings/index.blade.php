<x-app-layout>
<div class="py-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Explora anuncios</h1>
        <a href="{{ route('listings.create') }}" class="px-4 py-2 rounded bg-emerald-600 text-white">Publicar anuncio</a>
      </div>

      <form method="GET" action="{{ route('listings.index') }}" class="grid md:grid-cols-5 gap-3 mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar..."
               class="col-span-2 rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
        <input type="text" name="city" value="{{ request('city') }}" placeholder="Ciudad"
               class="rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700" />
        <select name="category_id" class="rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700">
          <option value="" @selected(!request()->filled('category_id'))>Todas las categorías</option>
          @foreach (($categories ?? []) as $cat)
            <option value="{{ $cat->id }}" @selected((string)request('category_id')===(string)$cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
        <select name="condition" class="rounded-md border-gray-300 dark:bg-gray-900 dark:border-gray-700">
          <option value="" @selected(!request()->filled('condition'))>Todos</option>
          @foreach (['new' => 'Nuevo','like_new'=>'Como nuevo','used'=>'Usado','for_parts'=>'Para piezas'] as $k=>$v)
            <option value="{{ $k }}" @selected(request('condition')===$k)>{{ $v }}</option>
          @endforeach
        </select>
        <div class="md:col-span-1">
          <button class="px-4 py-2 bg-emerald-600 text-white rounded-md w-full">Filtrar</button>
        </div>
      </form>

      @if($listings->count()===0)
        <p class="text-gray-500">No hay anuncios que coincidan.</p>
      @endif

      <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4">
        @foreach($listings as $listing)
          <div class="relative border rounded-md overflow-hidden hover:shadow">
            <a href="{{ route('listings.show',$listing) }}" class="block group">
              <div class="bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden h-40 md:h-44">
                @php $first = $listing->images->first() ?? null; @endphp
                @if($first && ($first->thumb_path || $first->path))
                  <img src="{{ asset('storage/'.($first->thumb_path ?? $first->path)) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                @else
                  <span class="text-gray-400 text-sm">Sin imagen</span>
                @endif
              </div>
              <div class="p-2">
                <div class="text-gray-900 dark:text-gray-100 font-medium truncate text-sm">{{ $listing->title }}</div>
                <div class="text-emerald-600 font-semibold text-sm">{{ $listing->price ? number_format($listing->price,2).' €' : '—' }}</div>
                <div class="text-xs text-gray-500">{{ $listing->city ?? '—' }}</div>
              </div>
            </a>

            <div class="absolute top-2 right-2">
              @php $fav = in_array($listing->id, $favoriteIds ?? []); @endphp
              <button type="button"
                      data-favorite-toggle
                      data-listing-id="{{ $listing->id }}"
                      data-favorited="{{ $fav ? '1' : '0' }}"
                      data-url-store="{{ route('favorites.store', $listing) }}"
                      data-url-destroy="{{ route('favorites.destroy', $listing) }}"
                      class="w-9 h-9 rounded-full bg-white/90 dark:bg-gray-800/90 border flex items-center justify-center hover:scale-105 transition"
                      title="{{ $fav ? 'Quitar de favoritos' : 'Añadir a favoritos' }}">
                <svg data-fav-filled xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-pink-600 {{ $fav ? '' : 'hidden' }}">
                  <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003a.75.75 0 01-.665 0z"/>
                </svg>
                <svg data-fav-outline xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5 text-pink-600 {{ $fav ? 'hidden' : '' }}">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 10.75c0 2.494-1.688 4.61-3.989 6.757a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.218l-.022.012-.007.003-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 3 13.244 3 10.75 3 8.264 4.988 6.5 7.125 6.5c1.19 0 2.228.5 2.997 1.278L12 9.656l1.878-1.878A4.223 4.223 0 0116.875 6.5C19.012 6.5 21 8.264 21 10.75z"/>
                </svg>
              </button>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-6">{{ $listings->withQueryString()->links() }}</div>
    </div>
  </div>
 </div>
</x-app-layout>

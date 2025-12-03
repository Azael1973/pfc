<x-app-layout>
<div class="py-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-8">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Bienvenido</h1>
        <p class="text-gray-600 dark:text-gray-400">Revisa los anuncios que has visto y explora por categorías.</p>
      </div>

      <div>
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Historial reciente</h2>
          <a href="{{ route('listings.index') }}" class="text-emerald-600 hover:underline text-sm">Ver todos los anuncios</a>
        </div>
        @if(!empty($recentListings) && count($recentListings) > 0)
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($recentListings as $listing)
              @php $first = $listing->images->first(); @endphp
              <a href="{{ route('listings.show', $listing) }}" class="block border rounded-lg overflow-hidden hover:shadow transition bg-gray-50 dark:bg-gray-900">
                <div class="h-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                  @if($first && ($first->thumb_path || $first->path))
                    <img src="{{ asset('storage/'.($first->thumb_path ?? $first->path)) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                  @else
                    <span class="text-gray-400 text-sm">Sin imagen</span>
                  @endif
                </div>
                <div class="p-3 space-y-1">
                  <div class="text-gray-900 dark:text-gray-100 font-medium truncate">{{ $listing->title }}</div>
                  <div class="text-emerald-600 font-semibold">{{ $listing->price ? number_format($listing->price,2).' €' : '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $listing->city ?? '—' }}</div>
                </div>
              </a>
            @endforeach
          </div>
        @else
          <p class="text-gray-500">Todavía no has visto ningún anuncio.</p>
        @endif
      </div>

      <div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">Explora por categoría</h2>
        @if($categories->count())
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach($categories as $cat)
              <a href="{{ route('listings.index', ['category_id' => $cat->id]) }}" class="block border rounded-lg p-4 text-center hover:shadow bg-gray-50 dark:bg-gray-900">
                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $cat->name }}</div>
                <div class="text-xs text-gray-500 mt-1">Ver anuncios</div>
              </a>
            @endforeach
          </div>
        @else
          <p class="text-gray-500">Aún no hay categorías creadas.</p>
        @endif
      </div>

      <div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-3">Subidos recientemente</h2>
        @if($recentUploads->count())
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
            @foreach($recentUploads as $listing)
              @php $first = $listing->images->first(); @endphp
              <a href="{{ route('listings.show', $listing) }}" class="block border rounded-lg overflow-hidden hover:shadow transition bg-gray-50 dark:bg-gray-900">
                <div class="h-36 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                  @if($first && ($first->thumb_path || $first->path))
                    <img src="{{ asset('storage/'.($first->thumb_path ?? $first->path)) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                  @else
                    <span class="text-gray-400 text-sm">Sin imagen</span>
                  @endif
                </div>
                <div class="p-3 space-y-1">
                  <div class="text-gray-900 dark:text-gray-100 font-medium truncate">{{ $listing->title }}</div>
                  <div class="text-emerald-600 font-semibold">{{ $listing->price ? number_format($listing->price,2).' €' : '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $listing->city ?? '—' }}</div>
                </div>
              </a>
            @endforeach
          </div>
        @else
          <p class="text-gray-500">Aún no hay anuncios publicados.</p>
        @endif
      </div>
    </div>
  </div>
</div>
</x-app-layout>

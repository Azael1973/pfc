<x-app-layout>
<div class="py-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Mis anuncios</h1>
        <a href="{{ route('listings.create') }}" class="px-4 py-2 rounded bg-emerald-600 text-white">Publicar anuncio</a>
      </div>

      @if($listings->count()===0)
        <p class="text-gray-500">Todavía no has creado anuncios.</p>
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

            <div class="p-2 flex justify-between items-center text-xs text-gray-600 dark:text-gray-300">
              @php
                $statusMap = ['available' => 'Disponible', 'reserved' => 'Reservado', 'sold' => 'Vendido'];
                $statusLabel = $statusMap[$listing->status] ?? ucfirst($listing->status);
              @endphp
              <span class="px-2 py-0.5 rounded {{ $listing->status==='available' ? 'bg-emerald-100 text-emerald-700' : ($listing->status==='reserved' ? 'bg-amber-100 text-amber-700' : 'bg-gray-200 text-gray-700') }}">{{ $statusLabel }}</span>
              <div class="flex gap-2">
                <a href="{{ route('listings.edit', $listing) }}" class="px-2 py-1 rounded bg-blue-600 text-white">Editar</a>
                <form method="POST" action="{{ route('listings.destroy', $listing) }}" data-confirm="¿Eliminar este anuncio?">
                  @csrf
                  @method('DELETE')
                  <button class="px-2 py-1 rounded bg-red-600 text-white">Eliminar</button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-6">{{ $listings->links() }}</div>
    </div>
  </div>
</div>
</x-app-layout>

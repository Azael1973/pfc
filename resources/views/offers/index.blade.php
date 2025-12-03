<x-app-layout>
<div class="py-6">
  <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-2 text-gray-900 dark:text-gray-100">Ofertas recibidas</h1>
      <p class="text-gray-600 mb-6">Anuncio: <a href="{{ route('listings.show', $listing) }}" class="text-blue-600">{{ $listing->title }}</a></p>

      @forelse($offers as $offer)
        <div class="border rounded p-4 mb-3 flex items-center justify-between">
          <div>
            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $offer->user->name }}</div>
            <div class="text-emerald-600 font-semibold">{{ number_format($offer->amount,2) }} €</div>
            <div class="text-sm text-gray-500">Estado: <span class="uppercase">{{ $offer->status }}</span></div>
            @if($offer->message)
              <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $offer->message }}</div>
            @endif
          </div>
          <div class="flex gap-2">
            <form method="POST" action="{{ route('offers.updateStatus', $offer) }}">@csrf @method('PATCH')
              <input type="hidden" name="status" value="accepted">
              <button class="px-3 py-1 rounded bg-emerald-600 text-white">Aceptar</button>
            </form>
            <form method="POST" action="{{ route('offers.updateStatus', $offer) }}">@csrf @method('PATCH')
              <input type="hidden" name="status" value="rejected">
              <button class="px-3 py-1 rounded bg-amber-600 text-white">Rechazar</button>
            </form>
          </div>
        </div>
      @empty
        <p class="text-gray-500">No hay ofertas aún.</p>
      @endforelse

      <div class="mt-6">{{ $offers->links() }}</div>
    </div>
  </div>
</div>
</x-app-layout>

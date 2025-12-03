<x-app-layout>
<div class="py-6">
  <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Conversación</h1>
      @if($conversation->listing)
        <div class="mb-4 text-sm text-gray-600">Anuncio: <a class="text-blue-600" href="{{ route('listings.show', $conversation->listing) }}">{{ $conversation->listing->title }}</a></div>
      @endif

      @php
        $me = auth()->id();
        $isOwner = $conversation->listing && $conversation->listing->user_id === $me;
        $latestPendingOffer = null;
        $acceptedOfferForMe = null;
        if ($conversation->listing) {
          $latestPendingOffer = $conversation->listing->offers()
            ->where('user_id', '!=', $conversation->listing->user_id)
            ->where('status', 'pending')
            ->latest()->first();
          $acceptedOfferForMe = $conversation->listing->offers()
            ->where('user_id', $me)
            ->where('status', 'accepted')
            ->latest()->first();
        }
      @endphp

      @if($isOwner && $latestPendingOffer)
        <div class="mb-4 p-4 rounded bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700">
          <div class="text-sm text-gray-800 dark:text-gray-100">Oferta pendiente: {{ number_format($latestPendingOffer->amount, 2) }} €</div>
          <div class="text-xs text-gray-600 dark:text-gray-300 mt-1">Enviada por: {{ $latestPendingOffer->user->name ?? 'Comprador' }}</div>
          <form method="POST" action="{{ route('offers.updateStatus', $latestPendingOffer) }}" class="mt-2">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="accepted">
            <button class="px-3 py-1 rounded bg-emerald-600 text-white text-sm">Aceptar oferta</button>
          </form>
        </div>
      @endif

      @if(!$isOwner && $acceptedOfferForMe)
        <div class="mb-4 p-4 rounded bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700">
          <div class="text-sm text-gray-800 dark:text-gray-100">Tu oferta ha sido aceptada por {{ number_format($acceptedOfferForMe->amount, 2) }} €</div>
          <a href="{{ route('listings.checkout', ['listing' => $conversation->listing, 'offer' => $acceptedOfferForMe->id]) }}" class="mt-2 inline-flex px-3 py-2 rounded bg-blue-600 text-white text-sm">Comprar ahora</a>
        </div>
      @endif

      <div class="space-y-3 mb-6">
        @php $me = auth()->id(); @endphp
        @foreach($conversation->messages()->with('user:id,name')->latest()->take(30)->get()->reverse() as $msg)
          <div class="flex {{ $msg->user_id === $me ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[80%] rounded px-3 py-2 {{ $msg->user_id === $me ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
              <div class="text-xs opacity-80 mb-1">{{ $msg->user->name }}</div>
              <div>{{ $msg->body }}</div>
              <div class="text-[10px] opacity-60 mt-1">{{ $msg->created_at->diffForHumans() }}</div>
            </div>
          </div>
        @endforeach
      </div>

      <form method="POST" action="{{ route('messages.store', $conversation) }}" class="flex gap-2">
        @csrf
        <input type="text" name="body" class="flex-1 rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" placeholder="Escribe un mensaje..." />
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Enviar</button>
      </form>
    </div>
  </div>
</div>
</x-app-layout>

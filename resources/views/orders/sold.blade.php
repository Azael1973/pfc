<x-app-layout>
<div class="py-6">
  <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Artículos vendidos</h1>

      @if($orders->count() === 0)
        <p class="text-gray-500">Aún no tienes ventas.</p>
      @endif

      <div class="space-y-3">
        @foreach($orders as $order)
          <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <div class="flex justify-between items-center">
              <div>
                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                  {{ $order->listing->title ?? 'Anuncio' }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  Pedido: {{ $order->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  Comprador ID: {{ $order->user_id }}
                </div>
              </div>
              <div class="flex items-center gap-3">
                @php
                  $statusLabel = [
                    'no_enviado' => 'No enviado',
                    'en_envio' => 'En envío',
                    'recibido' => 'Recibido',
                  ][$order->status] ?? $order->status;
                  $badgeClass = match($order->status) {
                    'no_enviado' => 'bg-gray-200 text-gray-800',
                    'en_envio' => 'bg-amber-100 text-amber-800',
                    'recibido' => 'bg-emerald-100 text-emerald-800',
                    default => 'bg-gray-200 text-gray-800',
                  };
                @endphp
                <span class="px-3 py-1 rounded-full text-sm {{ $badgeClass }}">{{ $statusLabel }}</span>
                @if($order->status === 'no_enviado')
                  <form method="POST" action="{{ route('orders.updateStatus', $order) }}" onsubmit="return confirm('Marcar como enviado?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="en_envio">
                    <button class="px-3 py-1 rounded bg-blue-600 text-white text-sm">Enviado</button>
                  </form>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-4">{{ $orders->links() }}</div>
    </div>
  </div>
</div>
</x-app-layout>

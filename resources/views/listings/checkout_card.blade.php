<x-app-layout>
<div class="py-6">
  <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <div class="mb-6">
        <a href="{{ route('listings.checkout', $listing) }}" class="text-sm text-emerald-600 hover:underline">&larr; Elegir otro método</a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-2">Pago con tarjeta</h1>
        <p class="text-gray-500 dark:text-gray-400">Introduce los datos de tu tarjeta para comprar <strong>{{ $listing->title }}</strong>.</p>
      </div>

      <div class="grid gap-6 md:grid-cols-3">
        <div class="md:col-span-2 space-y-4">
          <form id="card-payment-form" method="POST" action="{{ route('listings.checkout.card.process', $listing) }}" class="space-y-4">
            @csrf
            <div>
              <label class="block text-sm text-gray-700 dark:text-gray-300">Nombre del titular</label>
              <input type="text" name="card_holder" value="{{ old('card_holder') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" required>
              @error('card_holder') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm text-gray-700 dark:text-gray-300">Número de tarjeta</label>
              <input type="text" name="card_number" value="{{ old('card_number') }}" placeholder="1111 2222 3333 4444" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" required>
              @error('card_number') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300">Fecha de caducidad (MM/AA)</label>
                <input type="text" name="expiry" value="{{ old('expiry') }}" placeholder="08/27" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" required>
                @error('expiry') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
              </div>
              <div>
                <label class="block text-sm text-gray-700 dark:text-gray-300">CCV</label>
                <input type="text" name="ccv" value="{{ old('ccv') }}" placeholder="123" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" required>
                @error('ccv') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>
            @if($coupon)
              <input type="hidden" name="coupon_id" value="{{ $coupon->id }}">
            @endif
            @if($offer)
              <input type="hidden" name="offer_id" value="{{ $offer->id }}">
            @endif
            @error('coupon_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
          </form>
        </div>

        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3 h-fit">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resumen</h2>
          @php
            $basePrice = $effectivePrice ?? max(0, (float) ($listing->price ?? 0));
            $couponValue = $coupon ? min($coupon->value, $basePrice) : 0;
            $finalTotal = max(0, $basePrice - $couponValue);
            $pointsEarned = (int) floor($finalTotal);
          @endphp
          <div class="border rounded-md p-4 bg-white dark:bg-gray-800 space-y-1">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
              <span>Precio del anuncio</span>
              <span>{{ number_format($basePrice, 2) }} €</span>
            </div>
            @if($couponValue > 0)
              <div class="flex justify-between text-sm text-emerald-600">
                <span>Descuento ({{ $coupon->label }})</span>
                <span>-{{ number_format($couponValue, 2) }} €</span>
              </div>
            @endif
            <div class="flex justify-between text-lg font-semibold text-gray-900 dark:text-gray-100 border-t pt-2 mt-2">
              <span>Total</span>
              <span>{{ number_format($finalTotal, 2) }} €</span>
            </div>
          </div>
          <div class="text-sm text-gray-600 dark:text-gray-300">
            Obtendrás <span class="font-semibold">{{ $pointsEarned }}</span> puntos con esta compra.
          </div>
          <p class="text-xs text-gray-500 dark:text-gray-400">Tus datos se utilizarán exclusivamente para completar esta compra.</p>
          <button form="card-payment-form" class="w-full px-4 py-2 rounded bg-emerald-600 text-white">Pagar</button>
        </div>
      </div>
    </div>
  </div>
</div>
</x-app-layout>

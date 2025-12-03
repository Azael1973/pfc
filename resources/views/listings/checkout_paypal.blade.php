<x-app-layout>
<div class="py-6">
  <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <div class="mb-6">
        <a href="{{ route('listings.checkout', $listing) }}" class="text-sm text-emerald-600 hover:underline">&larr; Elegir otro método</a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-2">Pago con PayPal</h1>
        <p class="text-gray-500 dark:text-gray-400">Inicia sesión con tu cuenta PayPal para comprar <strong>{{ $listing->title }}</strong>.</p>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <form method="POST" action="{{ route('listings.checkout.paypal.process', $listing) }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" required>
            @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-300">Contraseña</label>
            <input type="password" name="password" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700" required>
            @error('password') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
          @if($coupon)
            <input type="hidden" name="coupon_id" value="{{ $coupon->id }}">
          @endif
          @if($offer)
            <input type="hidden" name="offer_id" value="{{ $offer->id }}">
          @endif
          @error('coupon_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
          <button class="px-4 py-2 rounded bg-emerald-600 text-white w-full">Continuar con PayPal</button>
        </form>

        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
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
        </div>
      </div>
    </div>
  </div>
</div>
</x-app-layout>

<x-app-layout>
<div class="py-6">
  <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <div class="mb-6">
        <a href="{{ route('listings.show', $listing) }}" class="text-sm text-emerald-600 hover:underline">&larr; Volver al anuncio</a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mt-2">Finaliza tu compra</h1>
        <p class="text-gray-500 dark:text-gray-400">Selecciona un método de pago para adquirir <strong>{{ $listing->title }}</strong>.</p>
      </div>

      <div class="grid gap-6 md:grid-cols-3">
        <div class="md:col-span-2 space-y-4">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Métodos de pago</h2>
          <form class="space-y-3" method="POST" action="{{ route('listings.checkout.select', $listing) }}">
            @csrf
            <label class="flex items-center justify-between border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-gray-900">
              <div>
                <div class="text-gray-900 dark:text-gray-100 font-medium">Tarjeta de débito</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Visa, MasterCard y más</div>
              </div>
              <input type="radio" name="payment_method" value="card" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500" {{ old('payment_method','card')==='card' ? 'checked' : '' }}>
            </label>
            <label class="flex items-center justify-between border rounded-lg px-4 py-3 cursor-pointer bg-white dark:bg-gray-900">
              <div>
                <div class="text-gray-900 dark:text-gray-100 font-medium">PayPal</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Usa tu cuenta de PayPal</div>
              </div>
              <input type="radio" name="payment_method" value="paypal" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500" {{ old('payment_method')==='paypal' ? 'checked' : '' }}>
            </label>
            <div class="mt-4">
              <label class="block text-sm text-gray-700 dark:text-gray-300">Cupón disponible</label>
              @if(($coupons ?? collect())->count())
                <select name="coupon_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-900 dark:border-gray-700">
                  <option value="" @selected(!old('coupon_id'))>No usar cupón</option>
                  @foreach($coupons as $coupon)
                    <option value="{{ $coupon->id }}" @selected(old('coupon_id')==$coupon->id)>
                      {{ $coupon->label }} ({{ number_format($coupon->value,2) }} €)
                    </option>
                  @endforeach
                </select>
              @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No tienes cupones disponibles.</p>
              @endif
              @error('coupon_id')
                <p class="text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>
            @error('payment_method')
              <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button class="mt-4 px-4 py-2 rounded bg-emerald-600 text-white">Continuar</button>
          </form>
        </div>

        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3 h-fit">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Resumen</h2>
          <div class="border rounded-md p-4 bg-white dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-400">Precio del anuncio</div>
            @php $basePrice = $effectivePrice ?? max(0, (float) ($listing->price ?? 0)); @endphp
            <div class="text-3xl font-bold text-emerald-600 mt-1">{{ number_format($basePrice, 2) }} €</div>
          </div>
          @php $pointsEarned = max(0, (int) floor($basePrice)); @endphp
          <div class="text-sm text-gray-600 dark:text-gray-300">
            Obtendrás <span class="font-semibold">{{ $pointsEarned }}</span> puntos con esta compra.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</x-app-layout>

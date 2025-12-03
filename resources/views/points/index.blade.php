<x-app-layout>
<div class="py-6">
  <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Tus puntos</h1>
        <p class="text-gray-600 dark:text-gray-300">
          Actualmente tienes <span class="font-semibold text-emerald-600">{{ $points }}</span> puntos.
        </p>
      </div>

      @if (session('status'))
        <div class="p-3 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
          {{ session('status') }}
        </div>
      @endif
      @error('reward')
        <div class="p-3 rounded bg-red-50 text-red-700 border border-red-200">
          {{ $message }}
        </div>
      @enderror

      <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Beneficios disponibles</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($rewards as $reward)
          @php
            $canRedeem = $points >= $reward['cost'];
          @endphp
          <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900 flex flex-col justify-between">
            <div class="space-y-2">
              <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $reward['label'] }}</div>
              <div class="text-sm text-gray-600 dark:text-gray-300">Costo: {{ $reward['cost'] }} puntos</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Valor: {{ $reward['value'] }} €</div>
            </div>
            <form method="POST" action="{{ route('points.redeem') }}" class="mt-4"
                  onsubmit="return confirm('¿Estás seguro de querer canjear la recompensa? Los puntos no se pueden devolver.');">
              @csrf
              <input type="hidden" name="reward" value="{{ $reward['key'] }}">
              <button class="w-full px-4 py-2 rounded {{ $canRedeem ? 'bg-emerald-600 text-white' : 'bg-gray-300 text-gray-600 cursor-not-allowed' }}"
                      {{ $canRedeem ? '' : 'disabled' }}>
                Canjear
              </button>
            </form>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
</x-app-layout>

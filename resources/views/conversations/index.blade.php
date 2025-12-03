<x-app-layout>
<div class="py-6">
  <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Conversaciones</h1>

      @forelse($conversations as $c)
        <div class="border rounded p-4 mb-3 hover:bg-gray-50 dark:hover:bg-gray-700 relative">
          <div class="absolute top-2 right-2">
            <details class="relative">
              <summary class="list-none cursor-pointer w-8 h-8 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">⋯</summary>
              <div class="absolute right-0 mt-1 w-36 bg-white dark:bg-gray-900 border rounded shadow">
                <form method="POST" action="{{ route('conversations.destroy', $c) }}" onsubmit="return confirm('¿Eliminar este chat? Esta acción afectará a ambos participantes.');">
                  @csrf
                  @method('DELETE')
                  <button class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">Eliminar chat</button>
                </form>
              </div>
            </details>
          </div>
          <a href="{{ route('conversations.show', $c) }}" class="block">
            <div class="font-medium text-gray-900 dark:text-gray-100">
              @if($c->listing)
                {{ $c->listing->title }}
              @else
                Chat
              @endif
            </div>
            <div class="text-sm text-gray-600 mt-1">
              Participantes: {{ $c->users->pluck('name')->join(', ') }}
            </div>
          </a>
        </div>
      @empty
        <p class="text-gray-500">No tienes conversaciones.</p>
      @endforelse

      <div class="mt-6">{{ $conversations->links() }}</div>
    </div>
  </div>
</div>
</x-app-layout>

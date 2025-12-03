<x-app-layout>
<div class="py-6">
  <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
      <h1 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Mensajes</h1>
      <div class="mb-3 text-sm text-gray-600">Conversación con: {{ $conversation->users->pluck('name')->join(', ') }}</div>

      <div class="space-y-3 mb-6">
        @php $me = auth()->id(); @endphp
        @foreach($messages->reverse() as $msg)
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

      <div class="mt-4">{{ $messages->links() }}</div>
    </div>
  </div>
</div>
</x-app-layout>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ecoshop') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            (function(){
                try {
                    var theme = localStorage.getItem('theme') || 'light';
                    if (theme === 'dark') document.documentElement.classList.add('dark');
                    else document.documentElement.classList.remove('dark');
                } catch (e) {}
            })();
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Modal de confirmación reutilizable -->
        <div id="confirm-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 flex">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <div class="mt-1 w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v4m0 4h.01M3 12c0-4.97 4.03-9 9-9s9 4.03 9 9-4.03 9-9 9-9-4.03-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">¿Estás seguro?</h3>
                        <p id="confirm-message" class="text-sm text-gray-600 dark:text-gray-300 mt-1"></p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" id="confirm-cancel" class="px-4 py-2 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">Cancelar</button>
                    <button type="button" id="confirm-accept" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Sí, continuar</button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const overlay = document.getElementById('confirm-overlay');
                const msgEl = document.getElementById('confirm-message');
                const btnCancel = document.getElementById('confirm-cancel');
                const btnAccept = document.getElementById('confirm-accept');
                let pendingForm = null;

                const closeModal = () => {
                    overlay.classList.add('hidden');
                    pendingForm = null;
                };

                document.querySelectorAll('form[data-confirm]').forEach(form => {
                    form.addEventListener('submit', (e) => {
                        if (form.dataset.confirmed === 'true') return;
                        e.preventDefault();
                        pendingForm = form;
                        msgEl.textContent = form.dataset.confirm || '¿Confirmar acción?';
                        overlay.classList.remove('hidden');
                    });
                });

                btnCancel.addEventListener('click', closeModal);
                overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });
                btnAccept.addEventListener('click', () => {
                    if (pendingForm) {
                        pendingForm.dataset.confirmed = 'true';
                        pendingForm.submit();
                    }
                    closeModal();
                });
            });
        </script>
    </body>
</html>

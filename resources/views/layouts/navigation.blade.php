<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('listings.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Inicio') }}
                    </x-nav-link>
                    <x-nav-link :href="route('listings.index')" :active="request()->routeIs('listings.*')">
                        {{ __('Anuncios') }}
                    </x-nav-link>
                    <x-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.*')">
                        {{ __('Favoritos') }}
                    </x-nav-link>
                    <x-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*') || request()->routeIs('messages.*')">
                        {{ __('Chats') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings / Auth Buttons -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <button id="themeToggle" type="button" class="me-4 inline-flex items-center justify-center w-9 h-9 rounded-full border text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900" title="Cambiar tema">
                    <svg id="iconSun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 hidden"><path d="M12 18a6 6 0 100-12 6 6 0 000 12z"/><path fill-rule="evenodd" d="M12 2.25a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zm0 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0V18a.75.75 0 01.75-.75zM4.72 4.72a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06L4.72 5.78a.75.75 0 010-1.06zm12.38 12.38a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06l-1.06-1.06a.75.75 0 010-1.06zM2.25 12a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H3a.75.75 0 01-.75-.75zm15 0a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H18a.75.75 0 01-.75-.75zM4.72 19.28a.75.75 0 010-1.06l1.06-1.06a.75.75 0 111.06 1.06L5.78 19.28a.75.75 0 01-1.06 0zm12.38-12.38a.75.75 0 010-1.06l1.06-1.06a.75.75 0 111.06 1.06l-1.06 1.06a.75.75 0 01-1.06 0z" clip-rule="evenodd"/></svg>
                    <svg id="iconMoon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M21.752 15.002A9.718 9.718 0 0112 21.75 9.75 9.75 0 1112 2.25a9.718 9.718 0 019.752 6.748 7.501 7.501 0 00-.412 6.004 7.5 7.5 0 00.412 5.999z"/></svg>
                </button>

                @auth
                    <a href="{{ route('points.index') }}" class="me-3 inline-flex items-center gap-2 px-3 py-2 rounded-full border text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.48 3.499a.75.75 0 011.04 0l2.122 2.122a2.25 2.25 0 001.591.659H19.5a.75.75 0 01.53 1.28l-2.012 2.012a2.25 2.25 0 000 3.182l2.012 2.012a.75.75 0 01-.53 1.28h-3.267a2.25 2.25 0 00-1.591.659l-2.122 2.122a.75.75 0 01-1.04 0l-2.122-2.122a2.25 2.25 0 00-1.591-.659H4.5a.75.75 0 01-.53-1.28l2.012-2.012a2.25 2.25 0 000-3.182L3.97 7.56A.75.75 0 014.5 6.28h3.267a2.25 2.25 0 001.591-.659l2.122-2.122z" />
                        </svg>
                        <span>{{ auth()->user()->points ?? 0 }}</span>
                    </a>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()?->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('listings.mine')">
                            Mis anuncios
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('orders.index')">
                            Pedidos
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('orders.sold')">
                            Vendidos
                        </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="me-2 inline-flex items-center px-4 py-2 rounded-md border text-sm font-medium text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-900">
                        {{ __('Iniciar sesión') }}
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700">
                        {{ __('Registrarse') }}
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('listings.index')" :active="request()->routeIs('listings.*')">
                {{ __('Anuncios') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.*')">
                {{ __('Favoritos') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('conversations.index')" :active="request()->routeIs('conversations.*') || request()->routeIs('messages.*')">
                {{ __('Chats') }}
            </x-responsive-nav-link>
            <button id="themeToggleMobile" type="button" class="ms-3 inline-flex items-center justify-center px-3 py-2 rounded-md border text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900">
                Tema
            </button>
        </div>

        <!-- Responsive Settings / Auth Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()?->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()?->email }}</div>
                    <a href="{{ route('points.index') }}" class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full border text-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.48 3.499a.75.75 0 011.04 0l2.122 2.122a2.25 2.25 0 001.591.659H19.5a.75.75 0 01.53 1.28l-2.012 2.012a2.25 2.25 0 000 3.182l2.012 2.012a.75.75 0 01-.53 1.28h-3.267a2.25 2.25 0 00-1.591.659l-2.122 2.122a.75.75 0 01-1.04 0l-2.122-2.122a2.25 2.25 0 00-1.591-.659H4.5a.75.75 0 01-.53-1.28l2.012-2.012a2.25 2.25 0 000-3.182L3.97 7.56A.75.75 0 014.5 6.28h3.267a2.25 2.25 0 001.591-.659l2.122-2.122z" />
                        </svg>
                        <span>{{ auth()->user()->points ?? 0 }} puntos</span>
                    </a>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('listings.mine')">
                        Mis anuncios
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1 px-4">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('Iniciar sesión') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Registrarse') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('orders.index')">
                        Pedidos
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('orders.sold')">
                        Vendidos
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @stack('meta')
    <title>{{ $title ?? '' }} - {{ config('app.name') }}</title>

    <link rel="preload" href="{{ asset('css/app.css') }}" as="style">

    <link rel="preconnect" href="{{ config('app.url') }}">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//unpkg.com">

    <link rel="prerender" href="{{ config('app.url') }}">

    <x-global-progress color="coral" />

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <livewire:styles />
    @stack('styles')

    <livewire:scripts />
    <script src="https://unpkg.com/alpinejs@3.4.2/dist/cdn.min.js" defer></script>
    @stack('scripts')
</head>

<body class="font-sans antialiased text-gray-600 bg-gray-100">

    <div class="flex">
        @include('layouts.sidebar')

        <div class="flex-1 @auth lg:ml-64 @endauth overflow-x-hidden">
            <div class="flex flex-col w-full h-screen min-h-screen">

                {{-- Mobile Menu --}}
                <div class="bg-white border-b shadow-sm md:hidden">
                    <x-section-centered class="flex items-center flex-1">
                        <div class="flex items-center h-14">
                            <div class="p-2 mr-2 -ml-2 rounded-full cursor-pointer hover:bg-gray-200"
                                x-on:click="$dispatch('opensidebar')" x-data x-cloak>
                                <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-600" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="0" y="0" width="24" height="24" stroke="none"></rect>
                                    <line x1="4" y1="6" x2="20" y2="6" />
                                    <line x1="4" y1="12" x2="20" y2="12" />
                                    <line x1="4" y1="18" x2="20" y2="18" />
                                </svg>
                            </div>
                            <x-application-logo class="h-8" />
                        </div>
                    </x-section-centered>
                </div>
                {{-- /Mobile Menu --}}

                @isset($topbar)
                    {{ $topbar }}
                @endisset

                <div class="flex flex-1 overflow-y-hidden">
                    <div class="flex-1 overflow-x-hidden overflow-y-auto">
                        @isset($secondaryTopbar)
                            {{ $secondaryTopbar }}
                        @endisset

                        <div class="@auth py-6 @endauth">
                            <x-section-centered>
                                <x-banner />
                            </x-section-centered>

                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-toast />
    <script>
        function toggleSubMenu(menuId) {
            const menu = document.getElementById(menuId);
            const icon = document.getElementById(menuId + '-icon');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.add('rotate-180'); // Menambahkan animasi rotate
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }
    </script>

</body>

</html>

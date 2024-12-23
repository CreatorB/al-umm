<x-guest-layout>
    @push('styles')
        <style>
            .pattern-grid-md {
                background-image: linear-gradient(currentColor 1px, transparent 1px),
                    linear-gradient(to right, currentColor 1px, transparent 1px);
                background-size: 40px 40px;
            }
        </style>
    @endpush

    <div
        class="relative flex flex-col items-center justify-center min-h-screen py-4 bg-gray-50 dark:bg-gray-900 sm:pt-0">
        @if (Route::has('login'))
            <div class="fixed top-0 right-0 flex items-center justify-end px-6 py-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 underline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 underline">Register</a>
                    @endif
                @endauth
            </div>
        @endif


        <div class="relative flex flex-col w-full max-w-4xl mx-auto overflow-hidden rounded-lg shadow-md">
            <div class="flex flex-col items-center justify-center flex-1 w-full p-6 bg-white md:p-12">
                <div class="relative z-40 w-full">
                    <div class="flex flex-col items-center mx-auto md:flex-row md:justify-start md:w-96">
                        <x-application-logo class="h-16" />
                        <div
                            class="mt-4 text-3xl font-semibold tracking-tighter text-center md:text-5xl md:mt-0 md:ml-4 md:text-left">
                            Al-Umm
                        </div>
                    </div>
                    <div
                        class="flex items-center mt-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase border-t border-gray-300 md:text-sm">
                        <div class="inline-flex px-4 mx-auto -mt-3 bg-white">
                            Sistem Induk Ma'had Al-Imam Asy-Syathiby
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 hidden md:block">
                <div class="-mb-10 -ml-12">
                    <div
                        class="w-6 h-6 mb-4 ml-8 bg-gray-100 border-4 border-gray-300 rounded-full md:w-10 md:h-10 md:border-8 md:mb-10 md:ml-32">
                    </div>
                    <div
                        class="w-16 h-16 p-4 mb-4 ml-2 bg-gray-300 rounded-full md:w-24 md:h-24 md:mb-10 md:ml-4 md:p-6">
                        <div class="w-8 h-8 bg-gray-100 rounded-full md:w-12 md:h-12"></div>
                    </div>
                    <div
                        class="w-6 h-10 ml-12 -mb-2 transform skew-y-6 bg-gray-300 md:w-10 md:h-20 md:skew-y-12 md:-mb-4 md:ml-32">
                    </div>
                    <div class="z-30 w-32 h-32 text-gray-300 md:w-64 md:h-64 pattern-grid-md"></div>
                </div>
            </div>

            <div class="absolute top-0 right-0 hidden transform rotate-180 md:block">
                <div class="-mt-10 -mr-12">
                    <div
                        class="w-6 h-6 mb-4 ml-8 bg-gray-100 border-4 border-gray-300 rounded-full md:w-10 md:h-10 md:border-8 md:mb-10 md:ml-32">
                    </div>
                    <div
                        class="w-16 h-16 p-4 mb-4 mr-2 bg-gray-300 rounded-full md:w-24 md:h-24 md:mb-10 md:mr-4 md:p-6">
                        <div class="w-8 h-8 bg-gray-100 rounded-full md:w-12 md:h-12"></div>
                    </div>
                    <div
                        class="w-6 h-10 ml-12 -mb-2 transform skew-y-6 bg-gray-300 md:w-10 md:h-20 md:skew-y-12 md:-mb-4 md:ml-32">
                    </div>
                    <div class="z-30 w-32 h-32 text-gray-300 md:w-64 md:h-64 pattern-grid-md"></div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

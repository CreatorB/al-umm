<x-app-layout title="Home">

    <x-slot name="topbar">
        <div class="bg-purple-700 text-purple-200 py-2.5 px-4 text-center text-sm">
            Sistem dalam pengembangan, kritik dan saran, silahkan disampaikan.
        </div>
        <x-navbar-top>
            <x-slot name="title">Home</x-slot>
        </x-navbar-top>
    </x-slot>

    <x-section-centered>
        @if (session()->has('error'))
            <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded alert-danger">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if (session()->has('success'))
            <div
                class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded alert-success">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        <x-grid mobile="1" tablet="3" laptop="3" desktop="3" gap="6">
            <div class="md:col-span-2">

                <x-heading class="mb-1" size="xl">List Menu</x-heading>
                <x-grid mobile="1" tablet="2" laptop="3" desktop="3" gap="6">
                    <div class="flex items-center justify-center h-16 bg-white rounded-lg shadow">
                        <button class="w-full h-full px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                            <a href="/attendances/tapping"
                                class="flex items-center justify-center block w-full h-full">Menu Absen</a>
                        </button>
                    </div>
                    {{-- <div class="h-16 bg-white rounded-lg shadow"></div>
                    <div class="h-16 bg-white rounded-lg shadow"></div> --}}
                </x-grid>

                <br><br>
                <x-heading class="mb-1" size="xl">QrCode</x-heading>
                <x-qrcode data="Welcome to Laravel Fresh" />

                <br><br>
                <x-heading class="mb-1" size="xl">Copy to Clipboard</x-heading>
                <x-copytoclipboard content="Welcome to Laravel Fresh" />
                <br>
                <x-copytoclipboard
                    content='<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                  </svg>' />

                <br><br>
                <x-heading class="mb-1" size="xl">Alertbox</x-heading>
                <x-alertbox>This is a new alertbox.</x-alertbox>
                <br>
                <x-alertbox variant="error" :close="false">Something went wrong. Please try again later.</x-alertbox>
                <br>
                <x-alertbox variant="success">
                    <strong class="block">Success</strong>
                    You've changed your password successfully. Please login with the new password next time.
                </x-alertbox>
            </div>

            <div>
                <div class="mb-5">
                    <x-card-weather-forecast api-key="ff9b41622f994b1287a73535210809" poll="6000" />
                </div>
                <div>
                    <livewire:events.calendar />
                </div>
            </div>
        </x-grid>

    </x-section-centered>
</x-app-layout>

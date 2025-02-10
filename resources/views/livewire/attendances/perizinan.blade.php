<div wire:key="tapping-component">
    <x-slot name="title">Perizinan</x-slot>
    <x-slot name="topbar">
        <x-navbar-top>
            <x-slot name="title">
                <x-breadcrumb>
                    <x-breadcrumb-item href="{{ route('attendances.perizinan') }}">Attendances</x-breadcrumb-item>
                    <x-breadcrumb-item>Perizinan</x-breadcrumb-item>
                </x-breadcrumb>
            </x-slot>
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
        <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded alert-danger">
            <strong class="font-bold">Awesomeness, </strong>
            <span class="block sm:inline">Work in progress</span>
        </div>
    </x-section-centered>
</div>

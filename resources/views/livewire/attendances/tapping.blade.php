<div wire:key="tapping-component">
    <x-slot name="title">Tapping</x-slot>
    <x-slot name="topbar">
        <x-navbar-top>
            <x-slot name="title">
                <x-breadcrumb>
                    <x-breadcrumb-item href="{{ route('attendances.tapping') }}">Attendances</x-breadcrumb-item>
                    <x-breadcrumb-item>Tapping</x-breadcrumb-item>
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
        <x-card-form>
            <x-slot name="title">Attendance Tapping</x-slot>
            <x-slot name="description">Tap to log your attendance</x-slot>

            <div class="flex justify-center space-x-4">
                @if ($canCheckIn)
                    <x-button color="green" with-spinner wire:click="checkIn" class="bg-green-600 hover:bg-green-700">
                        Check In
                    </x-button>
                @endif

                @if ($canCheckOut)
                    <x-button color="red" with-spinner wire:click="checkOut" class="bg-red-600 hover:bg-red-700">
                        Check Out
                    </x-button>
                @endif
            </div>
        </x-card-form>
    </x-section-centered>

    <script>
        Livewire.on('requestBrowserLocation', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        Livewire.emit('locationRetrieved', position.coords.latitude, position.coords.longitude);
                    },
                    (error) => {
                        console.error('Error retrieving location:', error);
                        Livewire.emit('locationFailed');
                    }
                );
            } else {
                console.error('Geolocation is not supported by this browser.');
                Livewire.emit('locationFailed');
            }
        });
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                @this.set('latitude', position.coords.latitude);
                @this.set('longitude', position.coords.longitude);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    </script>
</div>

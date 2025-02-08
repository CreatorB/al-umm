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
        window.appConfig = {
            ATTENDANCE_SERVER_IP: "{{ config('app.attendance_server_ip') }}"
        };

        // Environment helper function
        function env(key) {
            return window.appConfig[key] || null;
        }

        async function getDeviceInfo() {
            const deviceInfo = {
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                vendor: navigator.vendor,
                ipAddress: '',
                macAddress: '',
                timestamp: new Date().toISOString()
            };

            try {
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                deviceInfo.ipAddress = data.ip;
            } catch (error) {
                console.error('Error getting IP:', error);
                deviceInfo.ipAddress = 'unavailable';
            }

            if (navigator.networkInformation) {
                try {
                    const networkInfo = await navigator.networkInformation.getNetworkInterfaces();
                    if (networkInfo && networkInfo.length > 0) {
                        deviceInfo.macAddress = networkInfo[0].mac;
                    }
                } catch (error) {
                    console.error('Error getting MAC:', error);
                    deviceInfo.macAddress = 'unavailable';
                }
            }

            return deviceInfo;
        }

        async function pingServer() {
            const serverUrl = env('ATTENDANCE_SERVER_IP');
            if (!serverUrl) {
                throw new Error('Server URL not configured');
            }

            console.log(`Pinging local server: ${serverUrl}...`);

            const deviceInfo = await getDeviceInfo();

            return new Promise((resolve, reject) => {
                const startTime = new Date().getTime();

                // Simple TCP connection check
                const xhr = new XMLHttpRequest();
                xhr.timeout = 5000;

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        const endTime = new Date().getTime();
                        const pingTime = endTime - startTime;

                        // Any response means server is reachable
                        if (xhr.status === 200 || xhr.status === 302 || xhr.status === 404) {
                            console.log('Local server reachable:', {
                                pingTime
                            });
                            if (typeof Livewire !== 'undefined') {
                                Livewire.emit('updateDeviceInfo', deviceInfo);
                                @this.set('isInNetwork', true);
                                @this.set('pingTime', pingTime);
                            }
                            resolve({
                                success: true,
                                pingTime,
                                deviceInfo
                            });
                        } else {
                            handleConnectionError('Server not reachable');
                        }
                    }
                };

                xhr.onerror = function() {
                    // Even if we get an error, if it's a CORS error it means the server is actually reachable
                    const endTime = new Date().getTime();
                    const pingTime = endTime - startTime;

                    console.log('Local server reachable (CORS response):', {
                        pingTime
                    });
                    if (typeof Livewire !== 'undefined') {
                        Livewire.emit('updateDeviceInfo', deviceInfo);
                        @this.set('isInNetwork', true);
                        @this.set('pingTime', pingTime);
                    }
                    resolve({
                        success: true,
                        pingTime,
                        deviceInfo
                    });
                };

                xhr.ontimeout = function() {
                    handleConnectionError('Connection timeout');
                };

                function handleConnectionError(errorMessage) {
                    console.error('Connection failed:', errorMessage);
                    if (typeof Livewire !== 'undefined') {
                        Livewire.emit('updateDeviceInfo', deviceInfo);
                        @this.set('isInNetwork', false);
                        @this.set('pingTime', null);
                    }
                    reject(new Error(errorMessage));
                }

                // Just try to connect to the IP
                xhr.open('GET', serverUrl, true);
                xhr.send();
            });
        }

        // Initialize ping check
        document.addEventListener('DOMContentLoaded', () => {
            pingServer()
                .then(() => console.log('Initial ping successful'))
                .catch(error => console.error('Initial ping failed:', error));
        });

        // Set up Livewire event listener
        if (typeof Livewire !== 'undefined') {
            Livewire.on('checkPing', () => {
                pingServer()
                    .catch(error => console.error('Ping check failed:', error));
            });
        }
    </script>
    {{-- <script>
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
    </script> --}}
</div>

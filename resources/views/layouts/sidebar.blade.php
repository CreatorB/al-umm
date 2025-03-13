@auth
    <x-sidebar>
        <x-slot name="logo">
            <x-application-logo class="h-10" />
        </x-slot>

        <!-- Menu Home -->
        <x-nav-item to="{{ route('dashboard') }}" class="flex items-center py-2 text-gray-100">
            <x-iconic-home class="w-6 h-6 mr-4 text-gray-300 stroke-current" />Home
        </x-nav-item>

        <!-- Menu Apps -->
        @if (auth()->user()->hasAnyRole(['superadmin']))
        <x-nav-item no-margin>
            <div class="flex flex-col text-gray-100">
                <!-- Parent Menu -->
                <div class="flex items-center py-1 cursor-pointer" onclick="toggleSubMenu('apps-menu')">
                    <x-iconic-settings-sliders class="w-6 h-6 mr-4 text-gray-300 stroke-current" />
                    <span>Apps</span>
                    <x-iconic-chevron-down id="apps-menu-icon"
                        class="w-4 h-4 ml-auto transition-transform duration-300 transform" />
                </div>

                <!-- Submenu Apps -->
                <ul id="apps-menu" class="hidden pl-8 -mt-3 space-y-0">
                    <li>
                        <x-nav-item to="{{ route('admin.announcements.index') }}" class="flex items-center py-1 text-gray-100">
                            <x-iconic-arrow-right class="w-4 h-4 mr-2 text-gray-300 stroke-current" />Announcements
                        </x-nav-item>
                    </li>
                </ul>
            </div>
        </x-nav-item>
        @endif

        <!-- Menu Users -->
        @if (auth()->user()->hasAnyRole(['superadmin', 'admin', 'hr']))
            <x-nav-item to="{{ route('users') }}" class="flex items-center py-2 text-gray-100">
                <x-iconic-user class="w-6 h-6 mr-4 text-gray-300 stroke-current" />Users
            </x-nav-item>

            <x-nav-item to="{{ route('events.create') }}" class="flex items-center py-2 text-gray-100">
                <x-iconic-calendar class="w-6 h-6 mr-4 text-gray-300 stroke-current" />Events
            </x-nav-item>
        @endif

        <!-- Menu Roles -->
        @if (auth()->user()->hasAnyRole(['superadmin']))
            <x-nav-item to="{{ route('roles') }}" class="flex items-center py-2 text-gray-100">
                <x-iconic-lock class="w-6 h-6 mr-4 text-gray-300 stroke-current" />Roles
            </x-nav-item>
        @endif

        <!-- Menu Absensi -->
        <x-nav-item no-margin>
            <div class="flex flex-col text-gray-100">
                <!-- Parent Menu -->
                <div class="flex items-center py-1 cursor-pointer" onclick="toggleSubMenu('absensi-menu')">
                    <x-iconic-calendar class="w-6 h-6 mr-4 text-gray-300 stroke-current" />
                    <span>Absensi</span>
                    <x-iconic-chevron-down id="absensi-menu-icon"
                        class="w-4 h-4 ml-auto transition-transform duration-300 transform" />
                </div>

                <!-- Submenu Absensi -->
                <ul id="absensi-menu" class="hidden pl-8 -mt-3 space-y-0">
                    <li>
                        <x-nav-item to="{{ route('attendances.tapping') }}" class="flex items-center py-1 text-gray-100">
                            <x-iconic-arrow-right class="w-4 h-4 mr-2 text-gray-300 stroke-current" />Tapping
                        </x-nav-item>
                    </li>
                    <li>
                        <x-nav-item to="{{ route('attendances.perizinan') }}" class="flex items-center py-1 text-gray-100">
                            <x-iconic-arrow-right class="w-4 h-4 mr-2 text-gray-300 stroke-current" />Perizinan
                        </x-nav-item>
                    </li>
                </ul>
            </div>
        </x-nav-item>

        @if (auth()->user()->hasAnyRole(['superadmin', 'admin', 'hr']))
        <x-nav-item no-margin>
            <div class="flex flex-col text-gray-100">
                <div class="flex items-center py-1 cursor-pointer" onclick="toggleSubMenu('export-menu')">
                    <x-iconic-download class="w-6 h-6 mr-4 text-gray-300 stroke-current" />
                    <span>Export</span>
                    <x-iconic-chevron-down id="export-menu-icon"
                        class="w-4 h-4 ml-auto transition-transform duration-300 transform" />
                </div>

                <!-- Submenu: Export Absen -->
                <ul id="export-menu" class="hidden pl-8 -mt-3 space-y-0">
                    <li>
                        <x-nav-item to="{{ route('admin.export-absen') }}" class="flex items-center py-1 text-gray-100">
                            <x-iconic-arrow-right class="w-4 h-4 mr-2 text-gray-300 stroke-current" />Absen
                        </x-nav-item>
                    </li>
                    <li>
                        <x-nav-item to="{{ route('admin.export-users') }}" class="flex items-center py-1 text-gray-100">
                            <x-iconic-arrow-right class="w-4 h-4 mr-2 text-gray-300 stroke-current" />Users
                        </x-nav-item>
                    </li>
                </ul>
            </div>
        </x-nav-item>
        @endif

        <div class="my-3"></div>

        <!-- Footer -->
        <x-slot name="footer">
            <div class="px-4 py-2">
                <a href="{{ route('profile') }}"
                    class="flex px-2 py-2 hover:bg-gray-700 rounded-lg {{ request()->is('profile') ? 'bg-gray-700' : '' }}">
                    <livewire:profile-button />
                </a>
                <x-nav-item to="#" class="flex items-center py-2 mt-1 text-gray-100"
                    onclick="event.preventDefault(); document.getElementById('js-sidebar-logout').submit()">
                    <x-iconic-log-out class="w-6 h-6 mr-4 text-gray-300 stroke-current" />Log out
                </x-nav-item>
                <form method="POST" action="{{ route('logout') }}" id="js-sidebar-logout">
                    @csrf
                </form>
            </div>
        </x-slot>
    </x-sidebar>
@endauth

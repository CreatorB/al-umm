<div wire:key="tapping-component">
    <x-slot name="title">Export</x-slot>
    <x-slot name="topbar">
        <x-navbar-top>
            <x-slot name="title">
                <x-breadcrumb>
                    <x-breadcrumb-item href="{{ route('admin.export-absen') }}">Admin</x-breadcrumb-item>
                    <x-breadcrumb-item>Export Absen</x-breadcrumb-item>
                </x-breadcrumb>
            </x-slot>
        </x-navbar-top>
    </x-slot>
    <x-section-centered>
        <div>
            <form>
                <div class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-4 md:items-end">
                    <div class="w-full md:w-auto">
                        <label for="startDate" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                        <input type="date" id="startDate" wire:model="startDate"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('startDate')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="w-full md:w-auto">
                        <label for="endDate" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                        <input type="date" id="endDate" wire:model="endDate"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('endDate')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex w-full space-x-2 md:w-auto">
                        <button type="button" wire:click="exportRekap"
                            class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm md:flex-initial hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Export Rekap
                        </button>

                        <button type="button" wire:click="exportAbsen"
                            class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm md:flex-initial hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Export Absen
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-section-centered>
    <!-- Attendance List -->
    <div class="mt-8 px-4 sm:px-6 lg:px-8">
        <!-- Search & Filters -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <div class="flex-1 min-w-0">
                <input type="text" wire:model.debounce.300ms="search" placeholder="Search by name or Email..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="flex space-x-4">
                <input type="date" wire:model="selectedDate"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">

                <select wire:model="statusFilter"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="tugas_luar">Tugas Luar</option>
                    <option value="cuti">Cuti</option>
                    <option value="alpha">Alpha</option>
                </select>

                <button wire:click="resetFilters"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="mt-2 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">
                                        <a href="#" wire:click.prevent="sortBy('name')" class="group inline-flex">
                                            Name
                                            @if($sortField === 'name')
                                                <span class="ml-2">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <a href="#" wire:click.prevent="sortBy('check_in')" class="group inline-flex">
                                            Check In/Out Times
                                            @if($sortField === 'check_in')
                                                <span class="ml-2">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Location</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($attendances as $userId => $userAttendances)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                            {{ $userAttendances->first()->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $userAttendances->first()->email }}
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500">
                                            @foreach($userAttendances as $attendance)
                                                <div class="mb-1">
                                                    @if($attendance->check_in)
                                                        <span class="text-green-600">
                                                            {{ Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                                        </span>
                                                    @endif
                                                    @if($attendance->check_out)
                                                        <span class="text-red-600">
                                                            - {{ Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-500">
                                            @foreach($userAttendances as $attendance)
                                                <div class="mb-1">
                                                    <div class="text-xs">
                                                        @if($attendance->check_in_location)
                                                            IN: {{ $attendance->check_in_location }}
                                                        @endif
                                                    </div>
                                                    <div class="text-xs">
                                                        @if($attendance->check_out_location)
                                                            OUT: {{ $attendance->check_out_location }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span
                                                class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 
                                                {{ $userAttendances->first()->status === 'hadir' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $userAttendances->first()->status === 'sakit' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $userAttendances->first()->status === 'izin' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $userAttendances->first()->status === 'tugas_luar' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $userAttendances->first()->status === 'cuti' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $userAttendances->first()->status === 'alpha' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($userAttendances->first()->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">
                                            No attendance records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
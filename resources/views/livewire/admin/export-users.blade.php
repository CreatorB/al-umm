<div wire:key="tapping-component">
    <x-slot name="title">Users</x-slot>
    <x-slot name="topbar">
        <x-navbar-top>
            <x-slot name="title">
                <x-breadcrumb>
                    <x-breadcrumb-item href="{{ route('admin.export-users') }}">Admin</x-breadcrumb-item>
                    <x-breadcrumb-item>User Management</x-breadcrumb-item>
                </x-breadcrumb>
            </x-slot>
        </x-navbar-top>
    </x-slot>
    <x-section-centered>
        <!-- Pesan Session -->
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

        <!-- Tombol Download Template dan Upload -->
        <div class="flex mb-4 space-x-4">
            <button wire:click="downloadTemplate" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600">
                Download Template Excel
            </button>
            <input type="file" wire:model="file" class="px-4 py-2 border rounded">
            <button wire:click="uploadData" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">
                Upload Data
            </button>
        </div>

        <!-- Tabel Data User -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Email
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">NIP
                        </th>
                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <tr>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('users.edit', $user->id) }}"
                                    class="block font-medium text-indigo-500 truncate">
                                    {{ Str::title($user->name) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->nip }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-section-centered>
</div>

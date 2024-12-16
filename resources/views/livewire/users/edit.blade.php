<div>
    <script>
        Livewire.on('notify', (type, message) => {
            alert(message); // Replace with your custom notification UI
        });
    </script>
    <x-slot name="title">Edit User</x-slot>

    <x-slot name="topbar">
        <x-navbar-top>
            <x-slot name="title">
                <x-breadcrumb>
                    <x-breadcrumb-item href="{{ route('users') }}">Users</x-breadcrumb-item>
                    <x-breadcrumb-item>{{ $user->name }}</x-breadcrumb-item>
                </x-breadcrumb>
            </x-slot>
        </x-navbar-top>
    </x-slot>

    <x-section-centered>

        <x-card-form form-action="update">
            <x-slot name="title">Edit User</x-slot>

            <x-input label="Name" name="name" wire:model.defer="name" />

            <x-input type="email" label="Email" name="email" wire:model.defer="email" />

            <x-select label="Role" name="role" wire:model.defer="role">
                <option value="" disabled selected>Select a role</option>
                @foreach ($this->roles as $roleKey => $roleValue)
                    <option value="{{ $roleKey }}">{{ Str::title($roleValue) }}</option>
                @endforeach
            </x-select>

            <div class="mb-5">
                <x-label for="gender-input" class="mb-1">Gender</x-label>

                <div class="space-y-2">
                    <x-radio label="Male" id="male" name="gender" value="male" wire:model.defer="gender" />
                    <x-radio label="Female" id="female" name="gender" value="female" wire:model.defer="gender" />
                </div>

                <x-input-error for="gender" class="mt-1" />
            </div>

            <x-input-number type="tel" label="Phone" name="phone" wire:model.defer="phone" />

            <x-slot name="footer">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="mr-4">
                            <x-inline-toastr on="saved">Saved.</x-inline-toastr>
                        </div>

                        <x-button color="black" with-spinner wire:target="update">Save</x-button>
                    </div>
                </div>
            </x-slot>
        </x-card-form>

        @if (auth()->user()->hasAnyRole(['superadmin', 'admin']))
            <x-card class="flex justify-end">
                <div class="flex items-center space-x-4">
                    {{-- <x-users-user-status-switch :status="$user->status" /> --}}
                    {{-- <livewire:users.user-status-switch :status="$user->status" /> --}}
                    <livewire:users.user-status-switch :status="$user->status" :user-id="$user->id" />
                    <x-button color="red" wire:click="$set('confirmingUserDeletion', true)">Delete User</x-button>
                </div>
            </x-card>
        @endif
        @if ($confirmingUserDeletion)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                <div class="w-1/3 bg-white rounded-lg shadow-lg">
                    <div class="p-4 border-b">
                        <h5 class="text-lg font-bold">Confirm User Deletion</h5>
                    </div>
                    <div class="p-4">
                        <p>Are you sure you want to delete user {{ $user->name }}?</p>
                        <p class="text-sm text-gray-600">This action cannot be undone.</p>
                    </div>
                    <div class="flex justify-end p-4 space-x-2 border-t">
                        <x-button color="gray" wire:click="$set('confirmingUserDeletion', false)">Cancel</x-button>
                        <x-button color="red" wire:click="deleteUser({{ $user->id }})">Delete User</x-button>
                    </div>
                </div>
            </div>
        @endif

    </x-section-centered>
</div>

<div>
    <x-slot name="title">Edit User</x-slot>

    <x-slot name="topbar">
        <x-navbar-top>
            <x-slot name="title">
                <x-breadcrumb>
                    <x-breadcrumb-item href="{{ route('users') }}">Users</x-breadcrumb-item>
                    <x-breadcrumb-item>{{ $user->name }}</x-breadcrumb-item>
                </x-breadcrumb>
            </x-slot>
            <x-button color="red" wire:click="confirmDelete" class="flex items-center">
                <x-iconic-trash class="w-5 h-5 mr-2 -ml-1" />
                Delete User
            </x-button>
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
                    <x-radio label="Male" id="male" name="gender" value="male" wire:model.defer="gender"
                        checked />
                    <x-radio label="Female" id="female" name="gender" value="female" wire:model.defer="gender" />
                </div>

                <x-input-error for="gender" class="mt-1" />
            </div>

            <x-input-number type="tel" label="Phone" name="phone" wire:model.defer="phone" />

            <x-slot name="footer">
                <div class="mr-4">
                    <x-inline-toastr on="saved">Saved.</x-inline-toastr>
                </div>

                <x-button color="black" with-spinner wire:target="update">Save</x-button>
            </x-slot>
        </x-card-form>

        @if ($confirmingUserDeletion)
            <div class="modal show" style="display: block;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm User Deletion</h5>
                            <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete user {{ $user->name }}?<br>
                            This action cannot be undone.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                            <button type="button" class="btn btn-danger"
                                wire:click="deleteUser({{ $user->id }})">Delete User</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </x-section-centered>
</div>

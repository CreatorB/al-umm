<x-slot name="title">Edit Announcements</x-slot>
<x-slot name="topbar">
    <x-navbar-top>
        <x-slot name="title">
            <x-breadcrumb>
                <x-breadcrumb-item href="#">Apps</x-breadcrumb-item>
                <x-breadcrumb-item href="{{ route('admin.announcements.index') }}">Announcements</x-breadcrumb-item>
                <x-breadcrumb-item>Edit</x-breadcrumb-item>
            </x-breadcrumb>
        </x-slot>
    </x-navbar-top>
</x-slot>
<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <!-- Title -->
            <div class="mb-6">
                <x-label for="title" value="Title" />
                <x-input id="title" wire:model.defer="title" class="block mt-1 w-full" type="text" />
                <x-input-error for="title" class="mt-2" />
            </div>

            <!-- Type Selection -->
            <div class="mb-6">
                <x-label for="type" value="Type" />
                <x-select id="type" wire:model="type" class="block mt-1 w-full">
                    <option value="text">Text Only</option>
                    <option value="app_update">App Update</option>
                    <option value="link">Link</option>
                </x-select>
                <x-input-error for="type" class="mt-2" />
            </div>

            @if($type !== 'text')
                <!-- Link URL -->
                <div class="mb-6">
                    <x-label for="linkUrl" value="Link URL" />
                    <x-input id="linkUrl" wire:model.defer="linkUrl" class="block mt-1 w-full" type="url" />
                    <x-input-error for="linkUrl" class="mt-2" />
                </div>

                <!-- Link Type -->
                <div class="mb-6">
                    <x-label for="linkType" value="Link Type" />
                    <x-select id="linkType" wire:model.defer="linkType" class="block mt-1 w-full">
                        <option value="browser">Open in Browser</option>
                        <option value="app">Open/Install App</option>
                        <option value="deeplink">Deep Link</option>
                    </x-select>
                    <x-input-error for="linkType" class="mt-2" />
                </div>

                @if($type === 'app_update')
                    <!-- Version -->
                    <div class="mb-6">
                        <x-label for="version" value="Version" />
                        <x-input id="version" wire:model.defer="version" class="block mt-1 w-full" type="text" />
                        <x-input-error for="version" class="mt-2" />
                    </div>
                @endif
            @endif

            <!-- Content (CKEditor) -->
            <div class="mb-6" wire:ignore>
                <x-label for="editor" value="Content" />
                <div id="editor" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                <x-input-error for="content" class="mt-2" />
            </div>

            <!-- Roles -->
            <div class="mb-6">
                <x-label value="Target Roles" />
                <div class="mt-2 space-y-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.defer="selectedRoles" value="" class="form-checkbox">
                        <span class="ml-2">All Roles</span>
                    </label>
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input type="checkbox" wire:model.defer="selectedRoles" value="{{ $role }}"
                                class="form-checkbox">
                            <span class="ml-2">{{ ucfirst($role) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Priority -->
            <div class="mb-6">
                <x-label for="priority" value="Priority" />
                <x-input id="priority" wire:model.defer="priority" class="block mt-1 w-full" type="number" min="0" />
                <x-input-error for="priority" class="mt-2" />
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" wire:model.defer="isActive" class="form-checkbox">
                    <span class="ml-2">Active</span>
                </label>
            </div>

            <!-- Expired At -->
            <div class="mb-6">
                <x-label for="expiredAt" value="Expired At" />
                <x-input id="expiredAt" wire:model.defer="expiredAt" class="block mt-1 w-full" type="datetime-local" />
                <x-input-error for="expiredAt" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-2">
                <x-button type="button" color="gray" href="{{ route('admin.announcements.index') }}">
                    Cancel
                </x-button>
                <x-button type="submit">
                    Save Changes
                </x-button>
            </div>
        </div>
    </form>

    @push('scripts')
        <!-- Load CKEditor from CDN -->
        <script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>
        <script>
            document.addEventListener('livewire:load', function () {
                ClassicEditor
                    .create(document.querySelector('#editor'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                        placeholder: 'Type your content here...'
                    })
                    .then(editor => {
                        editor.model.document.on('change:data', () => {
                            @this.set('content', editor.getData());
                        });

                        // Handle Livewire reinits
                        window.livewire.on('reinit', () => {
                            editor.setData(@this.content);
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .ck-editor__editable {
                min-height: 200px;
                max-height: 400px;
            }
        </style>
    @endpush
</div>
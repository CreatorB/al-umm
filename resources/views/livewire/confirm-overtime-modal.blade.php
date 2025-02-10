<div>
    @if ($showModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="p-6 bg-white rounded-lg shadow-lg">
                <p class="mb-4">{{ $message }}</p>
                <div class="flex justify-end">
                    <button wire:click="confirm(true)" class="px-4 py-2 mr-2 text-white bg-blue-500 rounded">Ya</button>
                    <button wire:click="confirm(false)" class="px-4 py-2 text-white bg-gray-500 rounded">Tidak</button>
                </div>
            </div>
        </div>
    @endif
</div>
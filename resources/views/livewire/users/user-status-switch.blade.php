<div>
    <label for="status-toggle" class="flex items-center cursor-pointer">
        <span class="mr-2">{{ $status ? 'Active' : 'Inactive' }}</span>
        <input type="checkbox" id="status-toggle" wire:model="status" class="hidden">
        <div class="relative">
            <div class="block h-8 bg-gray-200 rounded-full w-14"></div>
            <div class="dot absolute left-1 top-1 w-6 h-6 bg-white rounded-full transition {{ $status ? 'translate-x-full bg-green-500' : 'bg-gray-400' }}"></div>
        </div>
    </label>
</div>

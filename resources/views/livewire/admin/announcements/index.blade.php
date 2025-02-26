<x-slot name="title">Announcements</x-slot>
<x-slot name="topbar">
    <x-navbar-top>
        <x-slot name="title">
            <x-breadcrumb>
                <x-breadcrumb-item href="#">Apps</x-breadcrumb-item>
                <x-breadcrumb-item>Announcements</x-breadcrumb-item>
            </x-breadcrumb>
        </x-slot>
    </x-navbar-top>
</x-slot>
<div>
   <div class="px-4 sm:px-6 lg:px-8">
       <div class="sm:flex sm:items-center">
           <div class="sm:flex-auto">
               <h1 class="text-xl font-semibold text-gray-900">Announcements List</h1>
           </div>
           <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
               <a href="{{ route('admin.announcements.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                   Create Announcement
               </a>
           </div>
       </div>

       <!-- Search and Filters -->
       <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0 sm:space-x-4">
           <div class="flex-1 min-w-0">
               <input type="text" wire:model.debounce.300ms="search" placeholder="Search announcements..."
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
           </div>

           <div class="flex items-center space-x-2">
               <select wire:model="filterRole"
                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                   <option value="">All Roles</option>
                   <option value="all">Global (No Role)</option>
                   @foreach($roles as $role)
                       <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                   @endforeach
               </select>

               <select wire:model="filterType"
                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                   <option value="">All Types</option>
                   @foreach($types as $value => $label)
                       <option value="{{ $value }}">{{ $label }}</option>
                   @endforeach
               </select>

               <button wire:click="resetFilters"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                   Reset
               </button>
           </div>
       </div>

       <!-- Table -->
       <div class="mt-8 flex flex-col">
           <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
               <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                   <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                       <table class="min-w-full divide-y divide-gray-300">
                           <thead class="bg-gray-50">
                               <tr>
                                   <th scope="col"
                                       class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                       <a href="#" wire:click.prevent="sortBy('title')" class="group inline-flex">
                                           Title
                                           @if($sortField === 'title')
                                               <span class="ml-2">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                           @endif
                                       </a>
                                   </th>
                                   <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                   <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                   <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Priority</th>
                                   <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Roles</th>
                                   <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                       <span class="sr-only">Actions</span>
                                   </th>
                               </tr>
                           </thead>
                           <tbody class="divide-y divide-gray-200 bg-white">
                               @forelse($announcements as $announcement)
                                   <tr>
                                       <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                           <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                               class="text-indigo-600 hover:text-indigo-900">
                                               {{ $announcement->title }}
                                           </a>
                                       </td>
                                       <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                           <x-badge :color="$announcement->type === 'app_update' ? 'red' : ($announcement->type === 'link' ? 'blue' : 'gray')">
                                               {{ $types[$announcement->type] }}
                                           </x-badge>
                                       </td>
                                       <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                           <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $announcement->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                               {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                                           </span>
                                       </td>
                                       <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                           {{ $announcement->priority }}
                                       </td>
                                       <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                           @if($announcement->roles)
                                               @foreach($announcement->roles as $role)
                                                   <x-badge color="blue">{{ $role }}</x-badge>
                                               @endforeach
                                           @else
                                               <x-badge color="purple">All</x-badge>
                                           @endif
                                       </td>
                                       <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                           <button wire:click="$emit('showDeleteConfirmation', {{ $announcement->id }})"
                                               class="text-red-600 hover:text-red-900">
                                               Delete
                                           </button>
                                       </td>
                                   </tr>
                               @empty
                                   <tr>
                                       <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                           No announcements found.
                                       </td>
                                   </tr>
                               @endforelse
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>
       </div>

       <!-- Pagination -->
       <div class="mt-4">
           {{ $announcements->links() }}
       </div>
   </div>

   <!-- Flash Messages -->
   @if (session()->has('success'))
       <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
           class="fixed bottom-0 right-0 m-6 p-4 rounded-lg bg-green-500 text-white shadow-lg">
           {{ session('success') }}
       </div>
   @endif

   <!-- Delete Confirmation Modal -->
   <script>
       window.addEventListener('livewire:load', function() {
           window.livewire.on('showDeleteConfirmation', announcementId => {
               if (confirm('Are you sure you want to delete this announcement?')) {
                   window.livewire.emit('delete', announcementId)
               }
           })
       })
   </script>
</div>
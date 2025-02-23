<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium text-gray-900">Edit User</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Update user information.
                    </p>
                </div>
            </div>

            <div class="mt-5 md:mt-0 md:col-span-2">
                <form wire:submit.prevent="update">
                    <div class="shadow sm:rounded-md sm:overflow-hidden">
                        <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                            <!-- Profile Photo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Photo</label>
                                <div class="mt-1 flex items-center">
                                    @if ($user->photo)
                                        <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}"
                                            class="h-20 w-20 rounded-full">
                                        <button type="button" wire:click="deletePhoto"
                                            class="ml-2 text-red-600 hover:text-red-900">
                                            Remove
                                        </button>
                                    @endif
                                    <input type="file" wire:model="photo"
                                        class="ml-5 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                @error('photo') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-6 gap-6">
                                <!-- Basic Information -->
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" wire:model="name"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('name') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model="email"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

<!-- Password Field -->
<div class="col-span-6 sm:col-span-3">
    <label class="block text-sm font-medium text-gray-700">
        Password 
        <span class="text-sm text-gray-500">(leave empty to keep current password)</span>
    </label>
    <input type="password" 
           wire:model="password" 
           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
           placeholder="Enter new password">
    @error('password') 
        <span class="text-sm text-red-500">{{ $message }}</span> 
    @enderror
</div>

<!-- Role field dengan info -->
<div class="col-span-6 sm:col-span-3">
    <label class="block text-sm font-medium text-gray-700">
        Role <span class="text-red-500">*</span>
        @if(auth()->user()->roles()->first()->name !== 'superadmin')
            <span class="text-sm text-gray-500">(can only assign admin and employee roles)</span>
        @endif
    </label>
    <select wire:model="role" 
            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option value="">Select Role</option>
        @foreach($roles as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>
    @error('role') 
        <span class="text-sm text-red-500">{{ $message }}</span> 
    @enderror
</div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Gender</label>
                                    <select wire:model="gender"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Gender</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    @error('gender') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" wire:model="phone"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('phone') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <!-- General Error Popup -->
                                <div x-data="{ show: @entangle('showErrorPopup') }">
                                    <div x-show="show" class="fixed inset-0 overflow-y-auto z-50" x-cloak>
                                        <div
                                            class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                            </div>

                                            <div
                                                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start">
                                                        <div
                                                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                            <svg class="h-6 w-6 text-red-600"
                                                                xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                        </div>
                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                                Form Validation Error
                                                            </h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500">
                                                                    Please check and fix the following errors:
                                                                </p>
                                                                <ul
                                                                    class="mt-3 list-disc list-inside text-sm text-red-600">
                                                                    @foreach($errorMessages as $message)
                                                                        <li>{{ $message }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button type="button" x-on:click="show = false"
                                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Employment Details -->
                                <div class="col-span-6 border-t pt-6 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Details</h3>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">NIP <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model.defer="userFields.nip"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('userFields.nip') <span class="text-sm text-red-500">{{ $message }}</span
                                    >                                    @enderror
                                </div>
                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Jabatan <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.defer="userFields.jabatan_id"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Jabatan</option>
                                        @foreach($jabatans as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('userFields.jabatan_id') <spa
                                       n class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Bagian</label>
                                    <select wire:model.defer="userFields.bagian_id"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Bagian</option>
                                        @foreach($bagians as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('userFields.bagian_id') <spa
                                       n class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Schedule <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.defer="userFields.schedule_id"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Schedule</option>
                                        @foreach($schedules as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('userFields.schedule_id') <spa
                                       n class="text-sm text-red-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Working Days <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model.defer="userFields.working_days"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Jumlah Cuti <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model.defer="userFields.jumlah_cuti"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <!-- Additional Information -->
                                <div class="col-span-6">
                                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                                    <textarea wire:model.defer="userFields.alamat" rows="3"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Status Pegawai</label>
                                    <input type="text" wire:model.defer="userFields.status_pegawai"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">No. Rekening</label>
                                    <input type="text" wire:model.defer="userFields.no_rek"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Gaji Pokok</label>
                                    <input type="number" wire:model.defer="userFields.gaji_pokok"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.defer="userFields.status"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <!-- Pendidikan Kedua -->
                                <div class="col-span-6 border-t pt-6 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pendidikan Kedua</h3>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Pendidikan</label>
                                    <input type="text" wire:model.defer="userFields.pendidikan_2"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                                    <input type="text" wire:model.defer="userFields.jurusan_pendidikan_2"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Sekolah/Universitas</label>
                                    <input type="text" wire:model.defer="userFields.sekolah_universitas_2"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Tahun Lulus</label>
                                    <input type="number" wire:model.defer="userFields.tahun_lulus_2"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <!-- Informasi Kontrak -->
                                <div class="col-span-6 border-t pt-6 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontrak</h3>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Kontrak Mulai
                                        (Pertama)</label>
                                    <input type="date" wire:model.defer="userFields.kontrak_mulai_1"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Kontrak Selesai
                                        (Pertama)</label>
                                    <input type="date" wire:model.defer="userFields.kontrak_selesai_1"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Kontrak Mulai
                                        (Kedua)</label>
                                    <input type="date" wire:model.defer="userFields.kontrak_mulai_2"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Kontrak Selesai
                                        (Kedua)</label>
                                    <input type="date" wire:model.defer="userFields.kontrak_selesai_2"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <!-- Informasi Tambahan -->
                                <div class="col-span-6 border-t pt-6 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Special Adjustment
                                        (SA)</label>
                                    <input type="number" step="0.01" wire:model.defer="userFields.special_adjustment_sa"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">SA Date Start Acting</label>
                                    <input type="date" wire:model.defer="userFields.sa_date_start_acting"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">PTT</label>
                                    <input type="text" wire:model.defer="userFields.ptt"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Tunjangan Jabatan</label>
                                    <input type="text" wire:model.defer="userFields.t_jabatan"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Tunjangan Kehadiran</label>
                                    <input type="text" wire:model.defer="userFields.t_kehadiran"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Tunjangan Anak</label>
                                    <input type="text" wire:model.defer="userFields.t_anak"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Bonus Sanad</label>
                                    <input type="text" wire:model.defer="userFields.bonus_sanad"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Diniyyah</label>
                                    <input type="text" wire:model.defer="userFields.diniyyah"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>

                        <!-- Save & Cancel Buttons -->
                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-2">
                            <button type="button" wire:click="cancelEdit" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md
                                text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2
                                focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-0 right-0 m-6 p-4 rounded-lg bg-green-500 text-white shadow-lg">
            {{ session('message') }}
        </div>
    @endif
</div>
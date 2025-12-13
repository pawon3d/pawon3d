<div>
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('role') }}"
                class="bg-[#313131] hover:bg-[#252324] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold text-base transition-colors"
                wire:navigate>
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                Kembali
            </a>
            <h1 class="text-xl font-semibold text-[#666666]">
                {{ $this->isEditMode() ? 'Rincian Peran' : 'Tambah Peran' }}
            </h1>
        </div>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        @if ($this->isEditMode())
            Anda dapat mengubah atau menghapus peran. Sesuaikan informasi jika terdapat perubahan, pastikan informasi
            yang dimasukan benar dan tepat. Informasi akan ditampilkan untuk mengetahui hak akses dan siapa saja pekerja
            yang memiliki peran tersebut.
        @else
            Tambah Peran. Lengkapi informasi yang diminta, pastikan informasi yang dimasukan benar dan tepat.
            Hak akses akan menentukan fitur apa saja yang dapat diakses oleh pekerja dengan peran ini.
        @endif
    </x-alert.info>

    <div class="flex flex-col gap-[30px] mt-5">
        <!-- Card: Nama Peran -->
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-8 py-6">
            <div class="flex flex-col gap-[30px]">
                <!-- Nama -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Nama</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Masukkan nama peran yang ingin ditambahkan.
                        </p>
                    </div>
                    <input type="text" wire:model="roleName" placeholder="Contoh: Koki A"
                        class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] placeholder:text-[#959595] focus:outline-none focus:border-[#666666]" />
                    @error('roleName')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Batas Pekerja -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Batas Pekerja</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Masukkan batas maksimal pekerja yang dapat memiliki peran ini. Kosongkan jika tidak ada
                            batasan.
                        </p>
                    </div>
                    <input type="number" wire:model="maxUsers" placeholder="Contoh: 5" min="1"
                        class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#adadad] rounded-[15px] text-base text-[#666666] placeholder:text-[#959595] focus:outline-none focus:border-[#666666]" />
                    @error('maxUsers')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Card: Pilih Hak Akses -->
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-8 py-6">
            <div class="flex flex-col gap-[30px]">
                <!-- Header -->
                <div class="flex flex-col gap-4">
                    <p class="text-base font-medium text-[#666666]">Pilih Hak Akses</p>
                    <p class="text-sm font-normal text-[#666666]">
                        Aktifkan satu atau beberapa hak akses untuk menampilkan dan memilih hak yang ingin diberikan.
                    </p>
                </div>

                <!-- Permission Categories -->
                <div class="flex flex-col gap-[35px]">
                    @foreach ($this->permissionCategories as $categoryKey => $category)
                        <div class="flex flex-col gap-4" wire:key="category-{{ $categoryKey }}">
                            <!-- Category Header with Toggle -->
                            <div class="flex flex-col gap-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-base font-normal text-[#666666]">
                                        {{ $category['label'] }}
                                    </p>
                                    <!-- Toggle Switch -->
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer"
                                            wire:model.live="categoryToggles.{{ $categoryKey }}"
                                            wire:change="toggleCategory('{{ $categoryKey }}')">
                                        <div
                                            class="w-[45px] h-[25px] bg-[#525252] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-[21px] after:w-[21px] after:transition-all peer-checked:bg-[#56C568]">
                                        </div>
                                    </label>
                                </div>
                                <p class="text-sm font-normal text-[#666666]">
                                    {{ $category['description'] }}
                                </p>
                            </div>

                            <!-- Permission Items (show when toggle is ON) -->
                            @if ($categoryToggles[$categoryKey])
                                <div class="flex flex-col gap-4 ml-4">
                                    @foreach ($category['permissions'] as $permissionName => $permissionDescription)
                                        <div class="flex items-center justify-between"
                                            wire:key="perm-{{ $permissionName }}">
                                            <ul class="list-disc ml-5">
                                                <li class="text-sm font-normal text-[#666666]">
                                                    {{ $permissionDescription }}
                                                </li>
                                            </ul>
                                            <!-- Checkbox -->
                                            <button type="button"
                                                wire:click="togglePermission('{{ $permissionName }}')"
                                                class="w-7 h-[22px] flex items-center justify-center cursor-pointer">
                                                @if ($this->isPermissionSelected($permissionName))
                                                    <svg class="w-5 h-5 text-[#56C568]" viewBox="0 0 24 24"
                                                        fill="currentColor">
                                                        <path
                                                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-[#666666]" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2">
                                                        <rect x="3" y="3" width="18" height="18" rx="2"
                                                            ry="2" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card: Daftar Pekerja (Only in Edit Mode) -->
        @if ($this->isEditMode())
            <div class="bg-[#fafafa] rounded-[15px] shadow-sm px-8 py-6">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <p class="text-base font-medium text-[#666666]">Daftar Pekerja</p>
                        <p class="text-sm font-normal text-[#666666]">
                            Daftar pekerja yang menggunakan peran ini.
                        </p>
                    </div>

                    <!-- Table -->
                    <div class="overflow-hidden rounded-[15px]">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-[#3F4E4F]">
                                    <th
                                        class="px-6 py-5 text-left text-sm font-bold text-[#f8f4e1] uppercase tracking-wider">
                                        Pekerja
                                    </th>
                                    <th
                                        class="px-6 py-5 text-left text-sm font-bold text-[#f8f4e1] uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th
                                        class="px-6 py-5 text-left text-sm font-bold text-[#f8f4e1] uppercase tracking-wider">
                                        No. Telepon
                                    </th>
                                    <th
                                        class="px-6 py-5 text-right text-sm font-bold text-[#f8f4e1] uppercase tracking-wider">
                                        Peran
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-[#fafafa] divide-y divide-white">
                                @forelse($users as $user)
                                    <tr wire:key="user-{{ $user->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#666666]">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#666666]">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#666666]">
                                            {{ $user->phone ?? '-' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#666666] text-right">
                                            {{ $roleName }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-4 text-center text-sm font-medium text-[#666666]">
                                            Tidak ada pekerja yang memiliki peran ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between mt-8">
        <!-- Delete Button (Only in Edit Mode) -->
        <div>
            @if ($this->isEditMode())
                <button type="button" wire:click="confirmDelete"
                    class="bg-[#eb5757] hover:bg-[#d64545] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f8f4e1] font-semibold text-base transition-colors cursor-pointer">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9zm7.5-5l-1-1h-5l-1 1H5v2h14V4h-3.5z" />
                    </svg>
                    Hapus Peran
                </button>
            @endif
        </div>

        <!-- Save Button -->
        <div class="flex gap-2.5">
            <button type="button" wire:click="save"
                class="bg-[#3F4E4F] hover:bg-[#2f3c3d] px-6 py-2.5 rounded-[15px] shadow-sm flex items-center gap-2 text-[#f8f4e1] font-semibold text-base transition-colors cursor-pointer">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" />
                </svg>
                {{ $this->isEditMode() ? 'Simpan Perubahan' : 'Simpan' }}
            </button>
        </div>
    </div>
</div>

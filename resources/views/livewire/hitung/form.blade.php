<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
        <flux:button variant="secondary"
            href="{{ $this->isEditMode() ? route('hitung.rincian', $hitung_id) : route('hitung.rencana') }}"
            wire:navigate icon="arrow-left" class="w-full sm:w-auto">
            Kembali
        </flux:button>
        <h1 class="text-xl font-semibold text-[#666666] text-center sm:text-left">
            {{ $this->isEditMode() ? 'Ubah Aksi' : 'Tambah Aksi' }}
        </h1>
    </div>

    {{-- Info Box --}}
    <x-alert.info>
        {{ $this->isEditMode() ? 'Ubah' : 'Tambahkan' }} aksi. Lengkapi informasi yang diminta, pastikan informasi yang
        dimasukan benar dan tepat.
        <span class="font-bold">Hitung Persediaan</span> untuk keakuratan jumlah barang secara fisik dengan sistem,
        <span class="font-bold">Catat Persediaan Rusak</span> untuk barang yang tidak layak konsumsi atau expired, dan
        <span class="font-bold">Catat Persediaan Hilang</span> untuk barang yang jumlahnya tidak sesuai dengan jumlah
        barang secara fisik.
    </x-alert.info>

    {{-- Form Card --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-24 gap-y-8">
            {{-- Left Column --}}
            <div class="space-y-8">
                {{-- Pilih Aksi --}}
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-base font-medium text-[#666666]">Pilih Aksi</label>
                        <p class="text-sm text-[#666666]">
                            Pilih aksi hitung atau padan (catat rusak atau hilang) persediaan sesuai dengan kebutuhan.
                        </p>
                    </div>
                    <flux:select wire:model.live="action" placeholder="Pilih Aksi Persediaan"
                        class="!bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px]">
                        <flux:select.option value="Hitung Persediaan">Hitung Persediaan</flux:select.option>
                        <flux:select.option value="Catat Persediaan Rusak">Catat Persediaan Rusak</flux:select.option>
                        <flux:select.option value="Catat Persediaan Hilang">Catat Persediaan Hilang</flux:select.option>
                    </flux:select>
                    <flux:error name="action" />
                </div>

                {{-- Tanggal Aksi --}}
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-base font-medium text-[#666666]">Tanggal Aksi</label>
                        <p class="text-sm text-[#666666]">
                            Masukkan tanggal aksi persediaan.
                        </p>
                    </div>
                    <div class="relative">
                        <input type="text"
                            class="w-full px-5 py-2.5 bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] text-[#666666] placeholder-[#959595] focus:outline-none focus:ring-1 focus:ring-[#666666]"
                            x-ref="datepicker" x-init="picker = new Pikaday({
                                field: $refs.datepicker,
                                format: 'DD MMM YYYY',
                                toString(date, format) {
                                    return moment(date).format('DD MMM YYYY');
                                },
                                parse(dateString, format) {
                                    return moment(dateString, 'DD MMM YYYY').toDate();
                                },
                                onSelect: function() {
                                    @this.set('hitung_date', moment(this.getDate()).format('DD MMM YYYY'));
                                }
                            });" wire:model.defer="hitung_date"
                            placeholder="dd mmm yyyy" readonly />
                        <flux:icon.calendar
                            class="absolute right-4 top-1/2 -translate-y-1/2 size-5 text-[#666666] pointer-events-none" />
                    </div>
                    <flux:error name="hitung_date" />
                </div>
            </div>

            {{-- Right Column --}}
            <div class="space-y-4 flex flex-col h-full">
                <div class="space-y-2">
                    <label class="text-base font-medium text-[#666666]">Catatan
                        {{ $this->isEditMode() ? 'Aksi' : 'Rencana Aksi' }}</label>
                    <p class="text-sm text-[#666666]">
                        Masukkan catatan {{ $this->isEditMode() ? 'aksi' : 'rencana aksi' }} apabila ada pesan atau
                        sesuatu yang penting untuk diberitahu.
                    </p>
                </div>
                <flux:textarea wire:model="note" rows="6" placeholder="Masukkan catatan"
                    class="flex-1 !bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px]" />
            </div>
        </div>
    </div>

    {{-- Daftar Persediaan Card --}}
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6">
        {{-- Header --}}
        <div class="space-y-4 mb-4">
            <h2 class="text-base font-medium text-[#666666]">Daftar Persediaan</h2>
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                <p class="text-sm text-[#666666] text-justify flex-1">
                    Tambah barang yang akan dilakukan hitung atau padan, barang dihitung atau dipadan agar jumlah dan
                    kondisi yang dimiliki secara fisik and sistem sama.
                </p>
                <flux:button variant="primary" wire:click="addDetail" icon="plus" class="w-full sm:w-auto">
                    Tambah Barang
                </flux:button>
            </div>
        </div>

        {{-- Table --}}
        <x-table.form
            :headers="[
                ['label' => 'Barang Persediaan', 'class' => 'px-6 py-4 text-left text-sm font-bold min-w-[200px]'],
                ['label' => 'Batch', 'class' => 'px-6 py-4 text-left text-sm font-bold min-w-[200px]'],
                ['label' => 'Jumlah', 'class' => 'px-6 py-4 text-right text-sm font-bold'],
                ['label' => 'Modal', 'class' => 'px-6 py-4 text-right text-sm font-bold'],
                ['label' => '', 'class' => 'px-6 py-4 w-16'],
            ]"
            bodyBg="bg-[#fafafa]"
        >
            <x-slot name="rows">
                @foreach ($hitung_details as $index => $detail)
                    <tr class="border-b border-[#d4d4d4]" wire:key="detail-{{ $index }}">
                        <td class="px-6 py-3">
                            <select
                                class="w-full bg-transparent border-0 text-sm text-[#666666] focus:outline-none focus:ring-0 cursor-pointer"
                                wire:model="hitung_details.{{ $index }}.material_id"
                                wire:change="setMaterial({{ $index }}, $event.target.value)">
                                <option value="" class="text-[#959595]">Pilih Barang</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-3">
                            <select
                                class="w-full bg-transparent border-0 text-sm text-[#666666] focus:outline-none focus:ring-0 cursor-pointer"
                                wire:model="hitung_details.{{ $index }}.material_batch_id"
                                wire:change="setBatch({{ $index }}, $event.target.value)">
                                @php
                                    $material = $materials->firstWhere('id', $detail['material_id']);
                                    $batches = $material?->batches ?? collect();
                                @endphp
                                <option value="" class="text-[#959595]">Pilih Batch</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->batch_number }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-3 text-right text-sm text-[#666666]">
                            {{ $detail['material_quantity'] }}{{ $detail['unit_name'] }}
                        </td>
                        <td class="px-6 py-3 text-right text-sm text-[#666666]">
                            Rp{{ number_format($detail['total'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if (count($hitung_details) > 1)
                                <button type="button" wire:click="removeDetail({{ $index }})"
                                    class="text-red-500 hover:text-red-700">
                                    <flux:icon.trash class="size-5" />
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-slot>
        </x-table.form>
        <flux:error name="hitung_details" />
        <flux:error name="hitung_details.*.material_id" />
        <flux:error name="hitung_details.*.material_batch_id" />
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8">
        <flux:button variant="filled" class="!bg-[#c4c4c4] !text-[#333333] hover:!bg-[#b0b0b0]"
            href="{{ $this->isEditMode() ? route('hitung.rincian', $hitung_id) : route('hitung.rencana') }}"
            wire:navigate icon="x-mark">
            Batal
        </flux:button>
        <flux:button variant="secondary" wire:click="save" icon="{{ $this->isEditMode() ? 'check' : 'archive-box' }}">
            {{ $this->isEditMode() ? 'Simpan Perubahan' : 'Buat Rencana Aksi' }}
        </flux:button>
    </div>
</div>

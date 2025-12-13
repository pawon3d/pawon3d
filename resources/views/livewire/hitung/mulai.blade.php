<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-4 items-center">
            <flux:button variant="secondary" icon="arrow-left" href="{{ route('hitung.rincian', $hitung_id) }}"
                class="px-6 py-2.5 bg-[#313131] rounded-[15px] shadow-sm flex items-center gap-2 text-[#f6f6f6] font-semibold"
                wire:navigate>
                Kembali
            </flux:button>
            <h1 class="text-xl font-semibold text-[#666666]">{{ $hitungAction }}</h1>
        </div>
        <div class="flex gap-2.5 items-center">
            <flux:button variant="secondary" wire:click="riwayatPembaruan">
                Riwayat Pembaruan
            </flux:button>
        </div>
    </div>

    <!-- Info Box -->
    <x-alert.info>
        <p>
            <strong>{{ $hitungAction }}.</strong> Masukkan hasil hitung secara bertahap dan jika terjadi kesalahan
            dalam memasukkan jumlah, masukkan jumlah pengurangan dengan tanda minus (-)
        </p>
    </x-alert.info>

    <!-- Daftar Persediaan Card -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-6 mb-8">
        <div class="flex justify-between items-center mb-5">
            <p class="text-base font-medium text-[#666666]">Daftar Persediaan</p>
            <flux:button variant="primary" type="button" wire:click="markAllAs">
                Tandai
                @if ($hitungAction === 'Hitung Persediaan')
                    Hitung
                @elseif ($hitungAction === 'Catat Persediaan Rusak')
                    Rusak
                @elseif ($hitungAction === 'Catat Persediaan Hilang')
                    Hilang
                @endif
                Semua
            </flux:button>
        </div>

        <div class="w-full">
            <!-- Table Header -->
            <div class="flex w-full rounded-t-[15px] overflow-hidden">
                <div class="bg-[#3F4E4F] px-6 py-5 min-w-[180px] w-[180px]">
                    <span class="text-sm font-bold text-[#F8F4E1] leading-tight block">Barang<br>Persediaan</span>
                </div>
                <div class="bg-[#3F4E4F] px-6 py-5 min-w-[100px] w-[100px]">
                    <span class="text-sm font-bold text-[#F8F4E1]">Batch</span>
                </div>
                <div class="bg-[#3F4E4F] px-6 py-5 flex-1 min-w-[100px] text-right">
                    <span class="text-sm font-bold text-[#F8F4E1] leading-tight block">Jumlah<br>Diharapkan</span>
                </div>
                @if ($hitungAction === 'Hitung Persediaan')
                    <div class="bg-[#3F4E4F] px-6 py-5 flex-1 min-w-[100px] text-right">
                        <span class="text-sm font-bold text-[#F8F4E1] leading-tight block">Selisih<br>Didapatkan</span>
                    </div>
                @else
                    <div class="bg-[#3F4E4F] px-6 py-5 flex-1 min-w-[100px] text-right">
                        <span class="text-sm font-bold text-[#F8F4E1] leading-tight block">Jumlah<br>Sebenarnya</span>
                    </div>
                @endif
                <div class="bg-[#3F4E4F] px-6 py-5 flex-1 min-w-[100px] text-center">
                    <span class="text-sm font-bold text-[#F8F4E1] leading-tight block">Barang<br>
                        @if ($hitungAction === 'Hitung Persediaan')
                            Terhitung
                        @elseif ($hitungAction === 'Catat Persediaan Rusak')
                            Rusak
                        @else
                            Hilang
                        @endif
                    </span>
                </div>
                <div class="bg-[#3F4E4F] px-6 py-5 flex-1 min-w-[100px] text-center">
                    <span class="text-sm font-bold text-[#F8F4E1] leading-tight block">Satuan<br>Ukur</span>
                </div>
                <div class="bg-[#3F4E4F] px-6 py-5 flex-1 min-w-[100px] text-right">
                    <span class="text-sm font-bold text-[#F8F4E1] leading-tight block">Jumlah<br>
                        @if ($hitungAction === 'Hitung Persediaan')
                            Terhitung
                        @elseif ($hitungAction === 'Catat Persediaan Rusak')
                            Rusak
                        @else
                            Hilang
                        @endif
                    </span>
                </div>
            </div>

            <!-- Table Body -->
            @foreach ($hitungDetails as $index => $detail)
                <div class="flex w-full border-b border-[#d4d4d4]">
                    <div class="bg-[#fafafa] px-6 py-4 min-w-[180px] w-[180px] flex items-center">
                        <span class="text-sm font-medium text-[#666666]">{{ $detail['material_name'] }}</span>
                    </div>
                    <div class="bg-[#fafafa] px-6 py-4 min-w-[100px] w-[100px] flex items-center whitespace-nowrap">
                        <span class="text-sm font-medium text-[#666666]">{{ $detail['batch_number'] }}</span>
                    </div>
                    <div class="bg-[#fafafa] px-6 py-4 flex-1 min-w-[100px] flex items-center justify-end">
                        <span class="text-sm font-medium text-[#666666]">
                            {{ $detail['quantity_expect'] }} {{ $detail['unit_alias'] }}
                        </span>
                    </div>
                    @if ($hitungAction === 'Hitung Persediaan')
                        <div class="bg-[#fafafa] px-6 py-4 flex-1 min-w-[100px] flex items-center justify-end">
                            @php
                                $selisih = $detail['selisih_didapatkan'];
                            @endphp
                            <span class="text-sm font-medium text-[#666666]">
                                {{ $selisih > 0 ? '+' : '' }}{{ $selisih }} {{ $detail['unit_alias'] }}
                            </span>
                        </div>
                    @else
                        <div class="bg-[#fafafa] px-6 py-4 flex-1 min-w-[100px] flex items-center justify-end">
                            <span class="text-sm font-medium text-[#666666]">
                                {{ $detail['jumlah_sebenarnya'] }} {{ $detail['unit_alias'] }}
                            </span>
                        </div>
                    @endif
                    <div class="bg-[#fafafa] px-6 py-4 flex-1 min-w-[100px] flex items-center justify-center">
                        <input type="number" placeholder="0" step="any"
                            wire:model.number.live.debounce.300ms="hitungDetails.{{ $index }}.quantity_input"
                            class="w-24 px-3 py-2 border rounded-lg text-center text-sm
                            {{ isset($errorInputs[$index]) ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#d4d4d4] focus:ring-[#3F4E4F] focus:border-[#3F4E4F]' }}" />
                    </div>
                    <div class="bg-[#fafafa] px-6 py-4 flex-1 min-w-[100px] flex items-center justify-center">
                        <span class="text-sm text-[#666666]">{{ $detail['unit_alias'] }}</span>
                    </div>
                    <div class="bg-[#fafafa] px-6 py-4 flex-1 min-w-[100px] flex flex-col items-end justify-center">
                        @php
                            $totalTerhitung = ($detail['quantity_actual'] ?? 0) + ($detail['quantity_input'] ?? 0);
                        @endphp
                        <span class="text-sm font-medium text-[#666666]">
                            {{ $totalTerhitung }} {{ $detail['unit_alias'] }}
                        </span>
                        @if (isset($errorInputs[$index]))
                            <span class="text-xs text-red-500 mt-1">{{ $errorInputs[$index] }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-4">
        <flux:button variant="filled" icon="x-mark" href="{{ route('hitung.rincian', $hitung_id) }}" wire:navigate>
            Batal
        </flux:button>
        <flux:button variant="secondary" icon="save" type="button" wire:click="save"
            class="px-6 py-2.5 bg-[#3F4E4F] rounded-[15px] shadow-sm flex items-center gap-2 text-[#F8F4E1] font-semibold">
            Simpan
        </flux:button>
    </div>

    <!-- Modal Riwayat Pembaruan -->
    <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Riwayat Pembaruan {{ $hitungNumber }}</flux:heading>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse ($activityLogs as $log)
                    <div class="border-b border-[#d4d4d4] py-3">
                        <div class="text-sm font-medium text-[#666666]">{{ $log['description'] }}</div>
                        <div class="text-xs text-[#999999]">
                            {{ $log['causer_name'] }} - {{ $log['created_at'] }}
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[#999999]">Belum ada riwayat pembaruan.</p>
                @endforelse
            </div>
        </div>
    </flux:modal>
</div>

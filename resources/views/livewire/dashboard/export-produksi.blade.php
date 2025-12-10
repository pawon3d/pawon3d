<div class="space-y-5">
    {{-- Back button and title --}}
    <div class="flex items-center gap-[15px]">
        <flux:button variant="primary" icon="arrow-left" href="{{ route('laporan-produksi') }}">
            Kembali
        </flux:button>
        <h1 class="text-[20px] font-semibold text-[#666666]">Unduh Laporan Produksi</h1>
    </div>

    {{-- Form Container --}}
    <div class="bg-[#fafafa] rounded-[15px] px-[30px] py-[25px]">
        <div class="flex flex-col gap-[30px] pb-[50px]">
            {{-- Report Content Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Pilih Isi Laporan Produksi <span class="text-[#eb5757]">*</span>
                </label>
                <flux:select wire:model.live="reportContent"
                    class="!bg-[#fafafa] !border-[#d4d4d4] !rounded-[15px] !px-[20px] !py-[10px]">
                    <option value="">Pilih Isi Laporan Produksi</option>
                    <option value="ringkasan">Ringkasan Produksi</option>
                    <option value="bahan">Penggunaan Bahan</option>
                    <option value="lengkap">Laporan Lengkap</option>
                </flux:select>
            </div>

            {{-- Date Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Tanggal Laporan <span class="text-[#eb5757]">*</span>
                </label>
                <div class="relative">
                    <input type="date" wire:model.live="selectedDate"
                        class="w-full bg-[#fafafa] border border-[#d4d4d4] rounded-[15px] px-[20px] py-[10px] text-[#959595] text-[16px] focus:outline-none focus:border-[#666666]">
                </div>
            </div>

            {{-- Worker Selection --}}
            <div class="flex flex-col gap-[15px] w-[500px]">
                <label class="text-[#666666] text-[16px] font-medium">
                    Pekerja <span class="text-[#eb5757]">*</span>
                </label>
                <flux:select wire:model.live="selectedWorker"
                    class="!bg-[#fafafa] !border-[#adadad] !rounded-[15px] !px-[20px] !py-[10px]">
                    <option value="semua">Semua Pekerja</option>
                    @foreach ($workers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Export Buttons --}}
    <div class="flex gap-[10px] justify-end">
        <flux:button
            onclick="window.open('{{ route('laporan-produksi.pdf') }}?selectedDate={{ $selectedDate }}&selectedWorker={{ $selectedWorker }}', '_blank')"
            class="!bg-[#eb5757] hover:!bg-[#d94444] !text-[#f6f6f6] !rounded-[15px] !px-[25px] !py-[10px] !font-semibold !shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon icon="document" class="size-5" />
            Unduh PDF
        </flux:button>
        <flux:button
            onclick="window.open('{{ route('laporan-produksi.excel') }}?selectedDate={{ $selectedDate }}&selectedWorker={{ $selectedWorker }}', '_blank')"
            class="!bg-[#56c568] hover:!bg-[#48b05a] !text-[#f6f6f6] !rounded-[15px] !px-[25px] !py-[10px] !font-semibold !shadow-[0px_2px_3px_0px_rgba(0,0,0,0.1)]">
            <flux:icon icon="table-cells" class="size-5" />
            Unduh Excel
        </flux:button>
    </div>
</div>

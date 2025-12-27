<div class="px-4 sm:px-[30px] py-4 sm:py-[30px]" style="background: #eaeaea; min-height: 100vh;">
    <!-- Header dengan tombol kembali dan judul -->
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-[50px] gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto sm:gap-[15px]">
            <flux:button href="{{ route('produksi', ['method' => 'siap-beli']) }}" icon="arrow-left" variant="secondary"
                wire:navigate class="w-full sm:w-auto flex justify-center">
                Kembali
            </flux:button>
            <h1 class="text-center sm:text-left"
                style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
                Rincian Produksi</h1>
        </div>

        @if ($status === 'Sedang Diproses')
            <div class="w-full sm:w-auto flex justify-center" style="height: 40px; padding-right: 2px;">
                <flux:button variant="secondary" wire:click="riwayatPembaruan" class="w-full sm:w-auto">
                    Riwayat Pembaruan
                </flux:button>
            </div>
        @endif
    </div>

        <div class="flex flex-col gap-[30px]">
            <div
                class="bg-[#fafafa] rounded-[15px] p-4 sm:p-[30px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] flex flex-col gap-[31px]">
                <!-- Header: ID dan Status -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <h2 class="text-[24px] sm:text-[30px] font-['Montserrat'] font-medium text-[#666666] m-0 text-center sm:text-left">
                        {{ $production->production_number }}
                    </h2>
                    <div
                        style="background: {{ $status === 'Belum Diproses' ? '#adadad' : ($status === 'Sedang Diproses' ? '#ffc400' : '#56c568') }}; padding: 8px 20px; border-radius: 30px;">
                        <span
                            style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; color: #fafafa;">{{ $status }}</span>
                    </div>
                </div>

                <!-- Info Tanggal dan Koki -->
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6">
                    <div class="flex flex-col md:flex-row flex-wrap gap-6 md:gap-[34px] justify-start">
                        <!-- Rencana Produksi -->
                        <div class="flex flex-col gap-[5px] text-left">
                            <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                                Rencana Produksi</p>
                            <div class="flex gap-[10px] font-['Montserrat'] text-[16px] text-[#666666]">
                                <span>{{ \Carbon\Carbon::parse($production->start_date)->translatedFormat('d M Y') }}</span>
                                <span>{{ \Carbon\Carbon::parse($production->time)->format('H:i') }}</span>
                            </div>
                        </div>

                        <!-- Tanggal Mulai Produksi -->
                        <div class="flex flex-col gap-[5px] text-left">
                            <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                                Tanggal Mulai Produksi</p>
                            <div class="flex gap-[10px] font-['Montserrat'] text-[16px] text-[#666666]">
                                @if ($production->date)
                                    <span>{{ \Carbon\Carbon::parse($production->date)->format('d M Y') }}</span>
                                    <span>{{ \Carbon\Carbon::parse($production->date)->format('H:i') }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </div>
                        </div>

                        <!-- Tanggal Produksi Selesai -->
                        <div class="flex flex-col gap-[5px] text-left">
                            <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                                Tanggal Produksi Selesai</p>
                            <p class="font-['Montserrat'] text-[16px] text-[#666666] m-0">
                                {{ $production->end_date ? \Carbon\Carbon::parse($production->end_date)->translatedFormat('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Koki -->
                    <div class="flex flex-col gap-[5px] text-left xl:text-right">
                        <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                            Koki</p>
                        <p class="font-['Montserrat'] text-[16px] text-[#666666] m-0">-
                        </p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="flex flex-col gap-[10px] sm:gap-[5px] w-full">
                    <div class="bg-[#eaeaea] h-[18px] sm:h-[45px] rounded-[5px] relative overflow-hidden">
                        <div class="bg-[#3f4e4f] h-full rounded-[5px] transition-[width] duration-300"
                            style="width: {{ $percentage }}%;">
                        </div>
                    </div>
                    <div class="text-center">
                        <span class="font-['Montserrat'] font-medium text-[12px] sm:text-[14px] text-[#525252]">
                            {{ number_format($percentage, 0) }}% ({{ $total_quantity_get }} dari {{ $total_quantity_plan }})
                        </span>
                    </div>
                </div>

                <!-- Rencana Produksi & Catatan -->
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
                        <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                            Rencana Produksi</p>
                        <button wire:click="buatCatatan"
                            class="w-full sm:w-auto bg-[#666666] text-[#f6f6f6] px-[25px] py-[10px] rounded-[15px] border-none cursor-pointer shadow-[0px_2px_3px_rgba(0,0,0,0.1)] flex items-center justify-center gap-[5px]">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path
                                    d="M10.586 0.586C10.96 0.211 11.469 0 12 0C12.531 0 13.04 0.211 13.414 0.586C13.789 0.96 14 1.469 14 2C14 2.531 13.789 3.04 13.414 3.414L4.414 12.414C4.082 12.746 3.658 12.97 3.196 13.06L0 14L0.94 10.804C1.03 10.342 1.254 9.918 1.586 9.586L10.586 0.586Z"
                                    fill="#fafafa" />
                            </svg>
                            <span class="font-['Montserrat'] font-semibold text-[16px]">Buat Catatan</span>
                        </button>
                    </div>

                    <div
                        style="background: #eaeaea; border: 1px solid #d4d4d4; border-radius: 15px; padding: 10px 20px; min-height: 120px;">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666; margin: 0; text-align: justify;">
                            {{ $production->note ?: 'Tidak ada catatan' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Daftar Produk -->
            <div
                class="bg-[#fafafa] rounded-[15px] p-4 sm:p-[30px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] flex flex-col gap-6 sm:gap-[30px]">
                <div class="flex flex-col gap-4 sm:gap-5">
                    <p class="font-['Montserrat'] font-medium text-[16px] text-[#666666] m-0">
                        Daftar Produk</p>

                    <!-- Tabel -->
                    <div class="overflow-x-auto w-full rounded-t-[15px]">
                        <div class="flex flex-col min-w-[900px]">
                        <!-- Header Tabel -->
                        <div
                            style="display: flex; background: #3f4e4f; border-radius: 15px 15px 0 0; overflow: hidden;">
                            <div
                                style="flex: 1; padding: 21px 25px; height: 60px; display: flex; align-items: center; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1;">Produk</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">Rencana<br>Produksi</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">Selisih<br>Didapatkan</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">Jumlah<br>Didapatkan</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 165px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">Pcs<br>Lebih</span>
                            </div>
                            <div
                                style="width: 115px; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; line-height: 1.2;">Pcs<br>Gagal</span>
                            </div>
                        </div>

                        <!-- Body Tabel -->
                        <div style="display: flex; flex-direction: column;">
                            @foreach ($production_details as $detail)
                                <div style="display: flex; background: #fafafa;">
                                    <div
                                        style="flex: 1; padding: 21px 25px; height: 60px; display: flex; align-items: center; min-width: 100px; border-bottom: 1px solid #d4d4d4;">
                                        <span
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $detail->product->name }}</span>
                                    </div>
                                    <div
                                        style="flex: 1; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px; border-bottom: 1px solid #d4d4d4;">
                                        <span
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right;">{{ $detail->quantity_plan }}</span>
                                    </div>
                                    <div
                                        style="flex: 1; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px; border-bottom: 1px solid #d4d4d4;">
                                        <span
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right;">{{ $detail->quantity_get - $detail->quantity_plan }}</span>
                                    </div>
                                    <div
                                        style="flex: 1; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px; border-bottom: 1px solid #d4d4d4;">
                                        <span
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right;">{{ $detail->quantity_get }}</span>
                                    </div>
                                    <div
                                        style="flex: 1; max-width: 165px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px; border-bottom: 1px solid #d4d4d4;">
                                        <span
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right;">{{ max(0, $detail->quantity_get - $detail->quantity_plan) }}</span>
                                    </div>
                                    <div
                                        style="width: 115px; max-width: 135px; padding: 21px 25px; height: 60px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px; border-bottom: 1px solid #d4d4d4;">
                                        <span
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right;">{{ $detail->quantity_fail }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer Tabel (Total) -->
                        <div style="display: flex; background: #eaeaea; height: 60px;">
                            <div
                                style="flex: 1; padding: 21px 25px; display: flex; align-items: center; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Total</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 135px; padding: 21px 25px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right;">{{ $total_quantity_plan }}</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 135px; padding: 21px 25px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right;">{{ $total_selisih }}</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 135px; padding: 21px 25px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right;">{{ $total_quantity_get }}</span>
                            </div>
                            <div
                                style="flex: 1; max-width: 165px; padding: 21px 25px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right;">{{ $total_pcs_lebih }}</span>
                            </div>
                            <div
                                style="width: 115px; max-width: 135px; padding: 21px 25px; display: flex; align-items: center; justify-content: flex-end; min-width: 100px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #666666; text-align: right;">{{ $total_pcs_gagal }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            @if ($status === 'Belum Diproses')
                <!-- Tombol Hapus -->
                <button wire:click="confirmDelete"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-[#eb5757] text-[#f8f4e1] px-[25px] py-[10px] rounded-[15px] border-none cursor-pointer shadow-[0px_2px_3px_rgba(0,0,0,0.1)]">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path
                            d="M8 3V1H12V3H17V5H15V19C15 19.2652 14.8946 19.5196 14.7071 19.7071C14.5196 19.8946 14.2652 20 14 20H6C5.73478 20 5.48043 19.8946 5.29289 19.7071C5.10536 19.5196 5 19.2652 5 19V5H3V3H8ZM7 8V17H9V8H7ZM11 8V17H13V8H11Z"
                            fill="currentColor" />
                    </svg>
                    <span class="font-['Montserrat'] font-semibold text-[16px]">Hapus Rencana Produksi</span>
                </button>

                <!-- Tombol Ubah & Mulai -->
                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    <button wire:click="ubahProduksi"
                        class="w-full sm:w-auto flex items-center justify-center gap-[5px] bg-[#feba17] text-[#f8f4e1] px-[25px] py-[10px] rounded-[15px] border-none cursor-pointer shadow-[0px_2px_3px_rgba(0,0,0,0.1)]"
                        wire:navigate>
                        <flux:icon icon="pencil-square" class="text-[#f6f6f6] shrink-0" />
                        <span class="font-['Montserrat'] font-bold text-[16px]">Ubah Produksi</span>
                    </button>

                    <flux:button variant="secondary" icon="chef-hat" wire:click="start" class="w-full sm:w-auto">
                        <span class="font-['Montserrat'] font-medium text-[16px]">Mulai Produksi</span>
                    </flux:button>
                </div>
            @else
                <!-- Spacer kosong di kiri saat Sedang Diproses -->
                <div></div>

                <!-- Tombol Selesaikan & Dapatkan Produk -->
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-[30px] items-center justify-end w-full sm:w-auto">
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-end w-full sm:w-auto">
                        <flux:button icon="check-circle" wire:click="selesaikanProduksi" class="w-full sm:w-auto">
                            <div style="padding: 0 5px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">Selesaikan
                                    Produksi</span>
                            </div>
                        </flux:button>
                        {{-- @if ($total_quantity_get < $total_quantity_plan) --}}
                        <flux:button wire:click="dapatkanProduk" icon="clipboard-document-list" variant="secondary" class="w-full sm:w-auto">
                            <div style="padding: 0 5px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">Dapatkan
                                    Produk</span>
                            </div>
                        </flux:button>
                        {{-- @endif --}}
                    </div>
                </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Catatan -->
    @if ($showNoteModal)
        <div
            style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 50;">
            <div style="background: #fafafa; border-radius: 15px; padding: 30px; width: 600px; max-width: 90%;">
                <h3
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin-bottom: 20px;">
                    Catatan Produksi
                </h3>
                <textarea wire:model="noteInput" rows="6"
                    style="width: 100%; padding: 15px; border: 1px solid #d4d4d4; border-radius: 10px; font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666; resize: vertical;"
                    placeholder="Tulis catatan..."></textarea>
                <div style="display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end;">
                    <button wire:click="$set('showNoteModal', false)"
                        style="background: #c4c4c4; color: #fff; padding: 10px 25px; border-radius: 15px; border: none; cursor: pointer; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                        Batal
                    </button>
                    <button wire:click="simpanCatatan"
                        style="background: #3f4e4f; color: #fff; padding: 10px 25px; border-radius: 15px; border: none; cursor: pointer; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('redirect', (event) => {
            window.location.href = event.url;
        });
    });
</script>

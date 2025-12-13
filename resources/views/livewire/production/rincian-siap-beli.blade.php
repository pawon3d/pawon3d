<div style="background: #eaeaea; min-height: 100vh; padding: 30px;">
    <!-- Header dengan tombol kembali dan judul -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 50px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <flux:button href="{{ route('produksi', ['method' => 'siap-beli']) }}" icon="arrow-left" variant="secondary"
                wire:navigate wire:navigate>
                Kembali
            </flux:button>
            <h1
                style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
                Rincian Produksi</h1>
        </div>

        @if ($status === 'Sedang Diproses')
            <div style="display: flex; gap: 10px; height: 40px; align-items: center; padding-right: 2px;">
                <flux:button variant="secondary" wire:click="riwayatPembaruan">
                    Riwayat Pembaruan
                </flux:button>
            </div>
        @endif
    </div>

    <div style="display: flex; flex-direction: column; gap: 50px;">
        <!-- Card Info Produksi -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <div
                style="background: #fafafa; border-radius: 15px; padding: 30px 25px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; gap: 31px;">
                <!-- Header: ID dan Status -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <h2
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 30px; color: #666666; margin: 0;">
                        {{ $production->production_number }}
                    </h2>
                    <div
                        style="background: {{ $status === 'Belum Diproses' ? '#adadad' : ($status === 'Sedang Diproses' ? '#ffc400' : '#56c568') }}; padding: 8px 20px; border-radius: 30px;">
                        <span
                            style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; color: #fafafa;">{{ $status }}</span>
                    </div>
                </div>

                <!-- Info Tanggal dan Koki -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 34px;">
                        <!-- Rencana Produksi -->
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <p
                                style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                                Rencana Produksi</p>
                            <div
                                style="display: flex; gap: 10px; font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666;">
                                <span>{{ \Carbon\Carbon::parse($production->start_date)->format('d M Y') }}</span>
                                <span>{{ $production->time }}</span>
                            </div>
                        </div>

                        <!-- Tanggal Mulai Produksi -->
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <p
                                style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                                Tanggal Mulai Produksi</p>
                            <div
                                style="display: flex; gap: 10px; font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666;">
                                @if ($production->date)
                                    <span>{{ \Carbon\Carbon::parse($production->date)->format('d M Y') }}</span>
                                    <span>{{ \Carbon\Carbon::parse($production->date)->format('H:i') }}</span>
                                @else
                                    <span>-</span>
                                @endif
                            </div>
                        </div>

                        <!-- Tanggal Produksi Selesai -->
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <p
                                style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                                Tanggal Produksi Selesai</p>
                            <p
                                style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666; margin: 0;">
                                {{ $production->end_date ? \Carbon\Carbon::parse($production->end_date)->format('d M Y H:i') : '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Koki (untuk siap-beli tidak ada worker, jadi tampilkan "-" atau nama default) -->
                    <div style="display: flex; flex-direction: column; gap: 5px; text-align: right;">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                            Koki</p>
                        <p style="font-family: 'Montserrat', sans-serif; font-size: 16px; color: #666666; margin: 0;">-
                        </p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div style="display: flex; flex-direction: column; gap: 5px; height: 45px; width: 100%;">
                    <div
                        style="background: #eaeaea; height: 100%; border-radius: 5px; position: relative; overflow: hidden;">
                        <div
                            style="background: #3f4e4f; height: 100%; width: {{ $percentage }}%; border-radius: 5px; transition: width 0.3s;">
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <span
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #525252;">{{ number_format($percentage, 0) }}%
                            ({{ $total_quantity_get }} dari {{ $total_quantity_plan }})</span>
                    </div>
                </div>

                <!-- Rencana Produksi & Catatan -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <p
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0; flex: 1;">
                            Rencana Produksi</p>
                        <button wire:click="buatCatatan"
                            style="background: #666666; color: #f6f6f6; padding: 10px 25px; border-radius: 15px; border: none; cursor: pointer; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px;">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path
                                    d="M10.586 0.586C10.96 0.211 11.469 0 12 0C12.531 0 13.04 0.211 13.414 0.586C13.789 0.96 14 1.469 14 2C14 2.531 13.789 3.04 13.414 3.414L4.414 12.414C4.082 12.746 3.658 12.97 3.196 13.06L0 14L0.94 10.804C1.03 10.342 1.254 9.918 1.586 9.586L10.586 0.586Z"
                                    fill="#fafafa" />
                            </svg>
                            <span style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">Buat
                                Catatan</span>
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
                style="background: #fafafa; border-radius: 15px; padding: 30px 25px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; flex-direction: column; gap: 30px;">
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <p
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                        Daftar Produk</p>

                    <!-- Tabel -->
                    <div style="display: flex; flex-direction: column;">
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
        <div style="display: flex; align-items: center; justify-content: space-between;">
            @if ($status === 'Belum Diproses')
                <!-- Tombol Hapus -->
                <button wire:click="confirmDelete"
                    style="background: #eb5757; color: #f8f4e1; padding: 10px 25px; border-radius: 15px; border: none; cursor: pointer; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path
                            d="M8 3V1H12V3H17V5H15V19C15 19.2652 14.8946 19.5196 14.7071 19.7071C14.5196 19.8946 14.2652 20 14 20H6C5.73478 20 5.48043 19.8946 5.29289 19.7071C5.10536 19.5196 5 19.2652 5 19V5H3V3H8ZM7 8V17H9V8H7ZM11 8V17H13V8H11Z"
                            fill="#f8f4e1" />
                    </svg>
                    <span style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">Hapus
                        Rencana Produksi</span>
                </button>

                <!-- Tombol Ubah & Mulai -->
                <div style="display: flex; gap: 10px;">
                    <button wire:click="ubahProduksi"
                        style="background: #feba17; color: #f8f4e1; padding: 10px 25px; border-radius: 15px; border: none; cursor: pointer; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px;"
                        wire:navigate>
                        <flux:icon icon="pencil-square" class="text-[#f6f6f6]" />
                        <span style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px;">Ubah
                            Produksi</span>
                    </button>

                    <flux:button variant="secondary" icon="chef-hat" wire:click="start">
                        <span style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px;">Mulai
                            Produksi</span>
                    </flux:button>
                </div>
            @else
                <!-- Spacer kosong di kiri saat Sedang Diproses -->
                <div></div>

                <!-- Tombol Selesaikan & Dapatkan Produk -->
                <div style="display: flex; gap: 30px; align-items: center; justify-content: flex-end;">
                    <div style="display: flex; gap: 10px; align-items: center; justify-content: flex-end;">
                        <flux:button icon="check-circle" wire:click="selesaikanProduksi">
                            <div style="padding: 0 5px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">Selesaikan
                                    Produksi</span>
                            </div>
                        </flux:button>
                        {{-- @if ($total_quantity_get < $total_quantity_plan) --}}
                        <flux:button wire:click="dapatkanProduk" icon="clipboard-document-list" variant="secondary">
                            <div style="padding: 0 5px;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">Dapatkan
                                    Produk</span>
                            </div>
                        </flux:button>
                        {{-- @endif --}}
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

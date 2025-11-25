<div>
    <div>
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div style="display: flex; gap: 15px; align-items: center;">
                <flux:button variant="secondary" icon="arrow-left"
                    href="{{ route('produksi.rincian-siap-beli', $production_id) }}">
                    Kembali
                </flux:button>
                <p
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
                    Dapatkan Produk</p>
            </div>
            <div>
                <button type="button" wire:click="riwayatPembaruan"
                    style="background: #525252; color: #ffffff; padding: 10px 25px; border-radius: 15px; border: 1px solid #666666; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; cursor: pointer;">
                    Riwayat Pembaruan
                </button>
            </div>
        </div>

        <!-- Info Penting Box -->
        <div
            style="background: #3f4e4f; padding: 24px 30px; border-radius: 20px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; gap: 20px; align-items: center; margin-bottom: 30px; min-height: 110px;">
            <svg style="width: 60px; height: 60px; flex-shrink: 0;" viewBox="0 0 60 60" fill="none">
                <path
                    d="M30 5C16.1929 5 5 16.1929 5 30C5 43.8071 16.1929 55 30 55C43.8071 55 55 43.8071 55 30C55 16.1929 43.8071 5 30 5ZM32.5 42.5H27.5V27.5H32.5V42.5ZM32.5 22.5H27.5V17.5H32.5V22.5Z"
                    fill="#dcd7c9" />
            </svg>
            <div
                style="flex: 1; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 14px; color: #dcd7c9; text-align: justify; line-height: normal;">
                Dapatkan Produk. Masukkan jumlah produksi yang selesai secara bertahap.
                <ul style="list-style-type: disc; margin: 0; padding-left: 21px;">
                    <li>Jika terjadi kesalahan dalam memasukkan jumlah, masukkan jumlah pengurangan dengan tanda minus
                        (-).</li>
                    <li>Masukkan jumlah pcs gagal jika produksi gagal.</li>
                    <li>Tandai ulang saat memasukan hasil produksi ulang dan hanya dapat dilakukan jika sebelumnya
                        terdapat kegagalan produksi.</li>
                </ul>
            </div>
        </div>

        <!-- Table Container -->
        <div
            style="background: #fafafa; padding: 25px 30px 30px; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 20px;">
                <p
                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                    Daftar Produk</p>
            </div>

            <!-- Table -->
            <div style="overflow: hidden; border-radius: 15px 15px 0 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #3f4e4f; height: 60px;">
                            <th
                                style="padding: 21px 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: left;">
                                Produk</th>
                            <th
                                style="padding: 21px 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; width: 135px; line-height: normal;">
                                Rencana<br>Produksi</th>
                            <th
                                style="padding: 21px 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; width: 135px; line-height: normal;">
                                Jumlah<br>Didapatkan</th>
                            <th
                                style="padding: 21px 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; width: 160px; line-height: normal;">
                                Adonan<br>Resep (Pcs)</th>
                            <th
                                style="padding: 21px 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; width: 120px; line-height: normal;">
                                Pcs<br>Gagal</th>
                            <th
                                style="padding: 21px 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; width: 110px; line-height: normal;">
                                Pcs<br>Lebih</th>
                            <th
                                style="padding: 21px 25px; font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #f8f4e1; text-align: right; width: 110px;">
                                Tandai<br>Ulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($production_details as $index => $detail)
                            <tr x-data="{ selectedProducts: @entangle('selectedProducts') }"
                                style="background: #fafafa; border-bottom: 1px solid #d4d4d4; height: 60px;">
                                <td
                                    style="padding: 0 25px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $detail['product_name'] ?? 'Produk Tidak Ditemukan' }}
                                </td>
                                <td
                                    style="padding: 0 25px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; width: 135px;">
                                    {{ $detail['quantity_plan'] }}
                                </td>
                                <td
                                    style="padding: 0 25px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; width: 135px;">
                                    {{ $detail['quantity_get'] }}
                                </td>
                                <td style="padding: 0 20px 0 25px; width: 160px;">
                                    <div style="display: flex; align-items: center; gap: 7px;">
                                        <input type="text"
                                            wire:model.lazy="production_details.{{ $index }}.recipe_quantity"
                                            placeholder="0"
                                            style="width: 71px; min-width: 70px; padding: 6px 10px; background: #fafafa; border: 1px solid #d4d4d4; border-radius: 5px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #959595; text-align: right; outline: none;" />
                                        <span
                                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666;">({{ $detail['quantity'] }})</span>
                                    </div>
                                </td>
                                <td style="padding: 0 25px; width: 120px;">
                                    <input type="number" placeholder="0"
                                        wire:model.number="production_details.{{ $index }}.quantity_fail"
                                        style="width: 100%; min-width: 70px; padding: 6px 10px; background: #fafafa; border: 1px solid #d4d4d4; border-radius: 5px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #959595; text-align: right; outline: none;" />
                                </td>
                                <td
                                    style="padding: 0 25px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-align: right; width: 110px;">
                                    {{ max(0, $detail['quantity_get'] - $detail['quantity_plan']) }}
                                </td>
                                <td style="padding: 0 25px; text-align: right; width: 110px;">
                                    <input type="checkbox" :value="'{{ $detail['id'] }}'" x-model="selectedProducts"
                                        {{ $detail['quantity_fail_raw'] == 0 ? 'disabled' : '' }}
                                        style="width: 22px; height: 22px; cursor: pointer; border-radius: 2px; border: 2px solid #525252;"
                                        :style="selectedProducts.includes('{{ $detail['id'] }}') ? 'accent-color: #56c568;' :
                                            ''" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Buttons -->
        <div style="display: flex; justify-content: flex-end; gap: 30px; margin-top: 60px;">
            <flux:button icon="x-circle" href="{{ route('produksi.rincian-siap-beli', $production_id) }}">
                Batal
            </flux:button>
            <flux:button variant="secondary" icon="bookmark-square" type="button" wire:click="save">
                Simpan
            </flux:button>
        </div>

        <!-- Modal Riwayat Pembaruan -->
        <flux:modal name="riwayat-pembaruan" class="w-full max-w-2xl" wire:model="showHistoryModal">
            <div class="space-y-6">
                <div>
                    <h1 size="lg">Riwayat Pembaruan {{ $production->production_number }}</h1>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @foreach ($activityLogs as $log)
                        <div class="border-b py-2">
                            <div class="text-sm font-medium">{{ $log->description }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $log->causer->name ?? 'System' }} -
                                {{ $log->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </flux:modal>
    </div>
</div>

<div>
    <div style="background: #eaeaea; min-height: 100vh; padding: 30px 0;">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div style="display: flex; gap: 15px; align-items: center;">
                <a href="{{ route('produksi.rincian', $production_id) }}" wire:navigate
                    style="background: #313131; color: #f6f6f6; padding: 10px 25px; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px; text-decoration: none; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                    <svg style="width: 20px; height: 20px; fill: #ffffff;" viewBox="0 0 20 20">
                        <path
                            d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18ZM8.70711 7.29289L6 10L8.70711 12.7071L9.41421 12L7.82843 10.5H14V9.5H7.82843L9.41421 8L8.70711 7.29289Z" />
                    </svg>
                    Kembali
                </a>
                <p
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
                    Dapatkan Produk</p>
            </div>
            <div>
                <flux:button variant="secondary" wire:click="riwayatPembaruan">
                    Riwayat Pembaruan
                </flux:button>
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
                                Jumlah<br>Pesanan</th>
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
                                    {{ $detail['cycle'] }}
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
            <a href="{{ route('produksi.rincian', $production_id) }}" wire:navigate
                style="background: #c4c4c4; color: #333333; padding: 10px 25px; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px; text-decoration: none; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                <svg style="width: 20px; height: 20px; fill: #333333;" viewBox="0 0 20 20">
                    <path
                        d="M10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2ZM13.0607 11.0607L12.3536 11.7678L10 9.41421L7.64645 11.7678L6.93934 11.0607L9.29289 8.70711L6.93934 6.35355L7.64645 5.64645L10 8L12.3536 5.64645L13.0607 6.35355L10.7071 8.70711L13.0607 11.0607Z" />
                </svg>
                Batal
            </a>
            <button type="button" wire:click="save"
                style="background: #3f4e4f; color: #f8f4e1; padding: 10px 25px; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 5px; border: none; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; cursor: pointer;">
                <svg style="width: 20px; height: 20px; fill: #f8f4e1;" viewBox="0 0 20 20">
                    <path
                        d="M15 2H5C3.89543 2 3 2.89543 3 4V16C3 17.1046 3.89543 18 5 18H15C16.1046 18 17 17.1046 17 16V4C17 2.89543 16.1046 2 15 2ZM7 2V6H13V2H7ZM10 14C8.89543 14 8 13.1046 8 12C8 10.8954 8.89543 10 10 10C11.1046 10 12 10.8954 12 12C12 13.1046 11.1046 14 10 14Z" />
                </svg>
                Simpan
            </button>
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

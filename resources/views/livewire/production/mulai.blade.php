<div>
    <div class="px-4 sm:px-[30px] py-4 sm:py-[30px]" style="background: #eaeaea; min-height: 100vh;">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-[30px] gap-4">
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto sm:gap-[15px]">
                <flux:button variant="secondary" icon="arrow-left" href="{{ route('produksi.rincian', $production_id) }}"
                    wire:navigate
                    class="w-full sm:w-auto"
                    style="background: #313131; color: #f6f6f6; padding: 10px 25px; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; justify-center; gap: 5px; text-decoration: none; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                    Kembali
                </flux:button>
                <p
                    style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
                    Dapatkan Produk</p>
            </div>
            <div class="w-full sm:w-auto">
                <flux:button variant="secondary" wire:click="riwayatPembaruan" class="w-full">
                    Riwayat Pembaruan
                </flux:button>
            </div>
        </div>

        <!-- Info Penting Box -->
        <x-alert.info>
            Dapatkan Produk. Masukkan jumlah produksi yang selesai secara bertahap.
            <ul style="list-style-type: disc; margin: 0; padding-left: 21px;">
                <li>Jika terjadi kesalahan dalam memasukkan jumlah, masukkan jumlah pengurangan dengan tanda minus
                    (-).</li>
                <li>Masukkan jumlah pcs gagal jika produksi gagal.</li>
                <li>Tandai ulang saat memasukan hasil produksi ulang dan hanya dapat dilakukan jika sebelumnya
                    terdapat kegagalan produksi.</li>
            </ul>
        </x-alert.info>

        <!-- Table Container -->
        <div class="bg-[#fafafa] p-4 sm:p-[30px] rounded-[15px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)]">
            <div style="margin-bottom: 20px;">
                <p
                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                    Daftar Produk</p>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto" style="border-radius: 15px 15px 0 0;">
                <table class="w-full min-w-[1000px]" style="border-collapse: collapse;">
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
        <div class="flex flex-col sm:flex-row justify-end gap-4 sm:gap-[30px] mt-[60px]">
            <flux:button variant="filled" icon="x-mark" href="{{ route('produksi.rincian', $production_id) }}"
                wire:navigate
                class="w-full sm:w-auto"
                style="background: #c4c4c4; color: #333333; padding: 10px 25px; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; gap: 5px; text-decoration: none; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">
                Batal
            </flux:button>
            <flux:button variant="secondary" icon="save" type="button" wire:click="save"
                class="w-full sm:w-auto"
                style="background: #3f4e4f; color: #f8f4e1; padding: 10px 25px; border-radius: 15px; box-shadow: 0px 2px 3px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; gap: 5px; border: none; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; cursor: pointer;">
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

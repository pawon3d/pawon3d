<div class="px-4 sm:px-0">
    <!-- Header dengan tombol kembali dan judul -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-[15px] mb-[30px]">
        <a href="{{ route('produksi') }}" wire:navigate
            class="w-full sm:w-auto flex items-center justify-center gap-[5px] px-[25px] py-[10px] bg-[#313131] text-white rounded-[15px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] no-underline"
            wire:navigate>
            <flux:icon.arrow-left style="width: 20px; height: 20px;" />
            <span style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px;">Kembali</span>
        </a>
        <h1 class="text-center sm:text-left" style="font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 20px; color: #666666; margin: 0;">
            Riwayat Produksi {{ $methodName }}
        </h1>
    </div>

    <!-- Container utama -->
    <div class="bg-[#fafafa] rounded-[15px] shadow-[0px_2px_3px_rgba(0,0,0,0.1)] p-4 sm:p-[30px]">
        <!-- Baris search dan filter -->
        <div class="flex flex-col sm:flex-row gap-4 sm:gap-5 items-center mb-5">
            <!-- Search bar -->
            <div
                class="flex-1 w-full bg-white border border-[#666666] rounded-[20px] flex items-center px-[15px] h-[40px]">
                <flux:icon.magnifying-glass style="width: 30px; height: 30px; color: #666666;" />
                <input wire:model.live="search" placeholder="Cari Produksi"
                    style="flex: 1; border: none; outline: none; padding: 10px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #959595;" />
            </div>

            <!-- Filter button -->
            <div class="flex items-center cursor-pointer justify-center">
                <div style="width: 25px; height: 25px; color: #666666;">
                    <svg viewBox="0 0 25 25" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10.4167 19.7917V16.6667H14.5833V19.7917H10.4167ZM6.25 14.0625V10.9375H18.75V14.0625H6.25ZM3.125 8.33333V5.20833H21.875V8.33333H3.125Z" />
                    </svg>
                </div>
                <div style="padding: 10px 5px;">
                    <p
                        style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 16px; color: #666666; margin: 0;">
                        Filter</p>
                </div>
            </div>
        </div>

        <!-- Tabel dengan custom component -->
        <div class="overflow-x-auto">
            <div class="min-w-[1000px]">
                <x-table.paginated :headers="[
                    ['label' => 'ID Produksi', 'field' => 'production_number', 'sortable' => true],
                    ['label' => 'Tanggal Selesai', 'field' => 'end_date', 'sortable' => true],
                    ['label' => 'Daftar Produk', 'field' => 'product_name', 'sortable' => false],
                    ['label' => 'Koki', 'field' => 'worker_name', 'sortable' => true],
                    ['label' => 'Status Produksi', 'field' => 'status', 'sortable' => true, 'multiline' => true],
                    ['label' => 'Kemajuan Produksi', 'field' => '', 'sortable' => false],
                ]" :paginator="$productions" :sortField="$sortField" :sortDirection="$sortDirection" headerBg="#3f4e4f"
                    headerText="#f8f4e1" bodyBg="#fafafa" bodyText="#666666">
            @foreach ($productions as $production)
                @php
                    $finishDate = $production->end_date ?? $production->updated_at;
                    $total_plan = $production->details->sum('quantity_plan');
                    $total_done = $production->details->sum('quantity_get');
                    $progress = $total_plan > 0 ? (int) (($total_done / $total_plan) * 100) : 0;
                    $progress = $progress > 100 ? 100 : $progress;
                @endphp
                <tr>
                    <!-- ID Produksi -->
                    <td style="padding: 0 25px; height: 60px;">
                        <a href="{{ route('produksi.rincian', $production->id) }}" wire:navigate
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; text-decoration: none;">
                            {{ $production->production_number }}
                        </a>
                    </td>

                    <!-- Tanggal Selesai -->
                    <td style="padding: 0 25px; height: 60px;">
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <div
                                style="display: flex; gap: 10px; font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666;">
                                <span>{{ \Carbon\Carbon::parse($finishDate)->translatedFormat('d M Y') }}</span>
                                <span>{{ \Carbon\Carbon::parse($finishDate)->format('H:i') }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- Daftar Produk -->
                    <td style="padding: 0 25px; height: 60px; max-width: 255px;">
                        <div
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            {{ $production->details->count() > 0
                                ? $production->details->map(fn($d) => $d->product?->name)->filter()->implode(', ')
                                : 'Tidak ada produk' }}
                        </div>
                    </td>

                    <!-- Koki -->
                    <td style="padding: 0 25px; height: 60px; max-width: 140px;">
                        <div
                            style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 14px; color: #666666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $production->workers->count() > 0
                                ? $production->workers->map(fn($w) => $w->worker?->name)->filter()->implode(', ')
                                : '-' }}
                        </div>
                    </td>

                    <!-- Status Produksi -->
                    <td style="padding: 0 25px; height: 60px;">
                        <div
                            style="background-color: #56c568; color: #fafafa; min-height: 40px; min-width: 90px; padding: 5px 15px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                            <span
                                style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 12px; text-align: center; white-space: pre-line;">Selesai</span>
                        </div>
                    </td>

                    <!-- Kemajuan Produksi -->
                    <td style="padding: 0 25px; height: 60px; min-width: 200px;">
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <!-- Progress bar -->
                            <div
                                style="width: 100%; height: 18px; background-color: #eaeaea; border-radius: 5px; overflow: hidden; position: relative;">
                                <div
                                    style="position: absolute; top: 0; left: 0; height: 100%; width: {{ $progress }}%; background-color: #49aa59; border-radius: 5px;">
                                </div>
                            </div>
                            <!-- Progress text -->
                            <div style="display: flex; gap: 5px; align-items: center; justify-content: center;">
                                <span
                                    style="font-family: 'Montserrat', sans-serif; font-weight: 500; font-size: 12px; color: #525252;">
                                    {{ $progress }}% ({{ $total_done }} dari {{ $total_plan }})
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
                </x-table.paginated>
            </div>
        </div>
    </div>
</div>

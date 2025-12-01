<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-normal text-gray-700">Ringkasan Umum</h1>
    </div>

    {{-- Dropdown Section Selector --}}
    <div class="mb-6">
        <flux:select wire:model.live="selectedSection" class="w-full max-w-full">
            @can('Kasir')
                <option value="kasir">Kasir</option>
            @endcan
            @can('Produksi')
                <option value="produksi">Produksi</option>
            @endcan
            @can('Inventori')
                <option value="inventori" selected>Inventori</option>
            @endcan
        </flux:select>
    </div>
    {{-- Calendar and Today's Expenses Section --}}
    <div class="flex flex-col overflow-hidden bg-white rounded-lg shadow p-6 md:flex-row gap-6" style="height: 26rem;">
        <!-- Kalender -->
        <div class="bg-white border border-gray-300 rounded-lg max-h-96 p-6 w-full md:w-1/3">
            <h2 class="text-base font-semibold mb-4">Kalender Inventori Pawon3D</h2>

            <!-- Navigasi Bulan -->
            <div class="flex items-center justify-between mb-4">
                <flux:button type="button" icon="chevron-left" wire:click="previousMonth" variant="ghost"
                    class="text-gray-500 hover:text-black" />
                <div class="font-normal text-sm text-center">
                    {{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F') }}
                    <br>{{ \Carbon\Carbon::parse($currentMonth)->year }}
                </div>
                <flux:button type="button" icon="chevron-right" wire:click="nextMonth" variant="ghost"
                    class="text-gray-500 hover:text-black" />
            </div>

            <!-- Hari -->
            <div class="grid grid-cols-7 text-center text-xs text-gray-500 font-medium mb-2">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                    <div @class([
                        $day === 'Min' || $day === 'Sab' ? 'text-red-500' : 'text-gray-700',
                    ])>{{ $day }}</div>
                @endforeach
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                @foreach ($calendar as $date => $info)
                    @php
                        $carbonDate = \Carbon\Carbon::parse($date);
                        $dayOfWeek = $carbonDate->translatedFormat('D');
                        $isWeekend = in_array($dayOfWeek, ['Min', 'Sab']);
                        $hasExpense = $info['hasExpense'] ?? false;
                    @endphp
                    <div class="flex flex-col items-center justify-center">
                        <button wire:click="selectDate('{{ $date }}')" @class([
                            'rounded-lg w-8 h-8 flex items-center justify-center transition-colors',
                            'bg-gray-800 text-white' => $selectedDate === $date,
                            'text-red-500' =>
                                $selectedDate !== $date && $isWeekend && $info['isCurrentMonth'],
                            'text-gray-800' =>
                                $selectedDate !== $date && !$isWeekend && $info['isCurrentMonth'],
                            'text-red-300' =>
                                $selectedDate !== $date && $isWeekend && !$info['isCurrentMonth'],
                            'text-gray-400' =>
                                $selectedDate !== $date && !$isWeekend && !$info['isCurrentMonth'],
                            'hover:bg-gray-100' => $selectedDate !== $date,
                        ])>
                            {{ $carbonDate->day }}
                        </button>
                        @if ($hasExpense)
                            <span class="w-1 h-1 bg-red-500 rounded-full mt-0.5"></span>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>

        <!-- Detail Belanja -->
        <div class="w-full md:w-2/3 overflow-y-auto scroll-hide">
            <h2 class="text-base font-normal mb-3">
                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F') }}
            </h2>

            <!-- Hari Ini -->
            <div class="space-y-3">
                @forelse($todayExpenses as $exp)
                    <div
                        class="bg-white border border-gray-200 rounded-lg p-4 flex items-start justify-between hover:shadow-sm transition-shadow">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                <span class="inline-block w-2 h-2 rounded-full bg-gray-800"></span>
                                <span>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</span>
                            </div>
                            <p class="font-semibold text-sm mb-1">
                                {{ strtoupper($exp->expense_number) }} ({{ $exp->supplier->name }})
                            </p>
                            <p class="text-xs text-gray-500">Belanja Persediaan</p>
                        </div>
                        <flux:button icon="chevron-right" type="button" variant="ghost" size="sm"
                            href="{{ route('belanja.rincian', $exp->id) }}" class="text-gray-400 hover:text-gray-800" />
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Tidak ada belanja pada hari ini.</p>
                @endforelse
            </div>

            <!-- Lainnya -->
            @if ($otherExpenses->count())
                <div class="mt-6">
                    <h3 class="text-sm font-normal mb-3 text-gray-600">Lainnya</h3>
                    <div class="space-y-3">
                        @foreach ($otherExpenses as $exp)
                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 flex items-start justify-between hover:shadow-sm transition-shadow">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                        <span class="inline-block w-2 h-2 rounded-full bg-gray-800"></span>
                                        <span>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</span>
                                    </div>
                                    <p class="font-semibold text-sm mb-1">
                                        {{ strtoupper($exp->expense_number) }} ({{ $exp->supplier->name }})
                                    </p>
                                    <p class="text-xs text-gray-500">Belanja Persediaan</p>
                                </div>
                                <flux:button icon="chevron-right" type="button" variant="ghost" size="sm"
                                    href="{{ route('belanja.rincian', $exp->id) }}"
                                    class="text-gray-400 hover:text-gray-800" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Belanja Persediaan --}}
    <div class="bg-white mt-8 rounded-lg shadow p-6">
        <div class="flex flex-row justify-between items-center mb-6">
            <h2 class="text-base font-semibold">Belanja Persediaan</h2>
        </div>

        <x-table.paginated :headers="[
            [
                'label' => 'Nomor Belanja',
                'sortable' => true,
                'sort-by' => 'expense_number',
                'align' => 'left',
                'class' => 'w-[150px]',
            ],
            [
                'label' => 'Tanggal Belanja',
                'sortable' => true,
                'sort-by' => 'expense_date',
                'align' => 'left',
                'class' => 'w-[140px]',
            ],
            [
                'label' => 'Toko Persediaan',
                'sortable' => true,
                'sort-by' => 'supplier_name',
                'align' => 'left',
                'class' => 'w-[140px]',
            ],
            [
                'label' => 'Status',
                'sortable' => true,
                'sort-by' => 'status',
                'align' => 'left',
                'class' => 'w-[120px]',
            ],
            ['label' => 'Barang Didapatkan', 'sortable' => false, 'align' => 'left', 'class' => 'w-[180px]'],
            [
                'label' => 'Total Harga (Perkiraan)',
                'sortable' => true,
                'sort-by' => 'grand_total_expect',
                'align' => 'left',
                'class' => 'w-[180px]',
            ],
            [
                'label' => 'Total Harga (Sebenarnya)',
                'sortable' => true,
                'sort-by' => 'grand_total_actual',
                'align' => 'left',
                'class' => 'w-[180px]',
            ],
        ]" :paginator="$expenses" emptyMessage="Belum ada belanja persediaan."
            headerBg="#3f4e4f" headerText="#f8f4e1">

            @foreach ($expenses as $expense)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                        <a href="{{ route('belanja.rincian', $expense->id) }}"
                            class="text-gray-900 hover:text-gray-600">
                            {{ $expense->expense_number }}
                        </a>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        {{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        {{ $expense->supplier->name ?? '-' }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                        <span @class([
                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                            'bg-yellow-500 text-white' => $expense->status === 'Sedang Diproses',
                            'bg-green-500 text-white' => $expense->status === 'Selesai',
                            'bg-gray-400 text-white' => $expense->status === 'Rere',
                        ])>
                            {{ $expense->status ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-700 align-top">
                        @php
                            $total_expect = $expense->expenseDetails->sum('quantity_expect');
                            $total_get = $expense->expenseDetails->sum('quantity_get');
                            $percentage = $total_expect > 0 ? ($total_get / $total_expect) * 100 : 0;
                            $percentage = min($percentage, 100);
                        @endphp
                        <span class="text-xs whitespace-nowrap">{{ number_format($percentage, 0) }}%
                            ({{ $total_get }} dari {{ $total_expect }})
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                        Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </x-table.paginated>
    </div>

    {{-- Persediaan Rendah --}}
    <div class="bg-white mt-8 rounded-lg shadow p-6">
        <div class="flex flex-row justify-between items-center mb-6">
            <h2 class="text-base font-semibold">Persediaan Rendah</h2>
        </div>

        <div class="bg-white rounded-xl border">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background-color: #3f4e4f;">
                        <tr>
                            <th class="px-6 py-5 text-left text-xs font-medium uppercase tracking-wider text-white">
                                Barang
                            </th>
                            <th class="px-6 py-5 text-right text-xs font-medium uppercase tracking-wider text-white">
                                Jumlah
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($materials as $material)
                            <tr class="hover:bg-gray-50 cursor-pointer transition"
                                wire:click="redirectToMaterial('{{ $material->id }}')">
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-900 align-top">
                                    {{ $material->name }}
                                </td>
                                <td class="px-6 py-5 text-right whitespace-nowrap text-sm text-gray-700 align-top">
                                    @php
                                        $quantity_main_total = 0;
                                        $batches = $material->batches;

                                        foreach ($batches as $b) {
                                            $detail = \App\Models\MaterialDetail::where('material_id', $material->id)
                                                ->where('unit_id', $b->unit_id)
                                                ->first();

                                            if ($detail) {
                                                $quantity_main = ($b->batch_quantity ?? 0) * ($detail->quantity ?? 0);
                                                $quantity_main_total += $quantity_main;
                                            }
                                        }

                                        $mainDetail = \App\Models\MaterialDetail::where('material_id', $material->id)
                                            ->where('is_main', true)
                                            ->first();
                                    @endphp
                                    {{ $batches->isNotEmpty() ? $quantity_main_total . ' ' . ($mainDetail->unit->alias ?? '') : 'Tidak Tersedia' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Tidak ada persediaan rendah
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Persediaan Hampir dan Telah Expired --}}
    <div class="bg-white mt-8 rounded-lg shadow p-6">
        <div class="flex flex-row justify-between items-center mb-6">
            <h2 class="text-base font-semibold">Persediaan Hampir dan Telah Expired</h2>
        </div>

        <div class="bg-white rounded-xl border">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background-color: #3f4e4f;">
                        <tr>
                            <th
                                class="px-6 py-5 text-left text-xs font-medium uppercase tracking-wider text-white w-[200px]">
                                Barang
                            </th>
                            <th
                                class="px-6 py-5 text-left text-xs font-medium uppercase tracking-wider text-white w-[150px]">
                                Batch
                            </th>
                            <th
                                class="px-6 py-5 text-left text-xs font-medium uppercase tracking-wider text-white w-[150px]">
                                Tanggal Expired
                            </th>
                            <th
                                class="px-6 py-5 text-left text-xs font-medium uppercase tracking-wider text-white w-[140px]">
                                Status
                            </th>
                            <th
                                class="px-6 py-5 text-right text-xs font-medium uppercase tracking-wider text-white w-auto">
                                Jumlah
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $hasExpired = false;
                        @endphp
                        @foreach ($materialB as $material)
                            @php
                                $batches = $material->batches;
                                $today = now();
                                $nearFuture = now()->addMonth();

                                $filteredBatches = $batches->filter(function ($batch) use ($today, $nearFuture) {
                                    $date = \Carbon\Carbon::parse($batch->date);
                                    return $date->lessThanOrEqualTo($nearFuture);
                                });
                            @endphp

                            @foreach ($filteredBatches as $batch)
                                @php
                                    $hasExpired = true;
                                @endphp
                                <tr class="hover:bg-gray-50 cursor-pointer transition"
                                    wire:click="redirectToMaterial('{{ $material->id }}')">
                                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-900 align-top">
                                        {{ $material->name }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                                        {{ $batch->batch_number ?? '-' }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 align-top">
                                        {{ \Carbon\Carbon::parse($batch->date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-sm align-top">
                                        @if (\Carbon\Carbon::parse($batch->date)->isPast())
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                                Expired
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white">
                                                Hampir Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap text-sm text-gray-700 align-top">
                                        {{ $batch->batch_quantity }} {{ $batch->unit->alias ?? '' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                        @if (!$hasExpired)
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Tidak ada persediaan yang hampir atau telah expired
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

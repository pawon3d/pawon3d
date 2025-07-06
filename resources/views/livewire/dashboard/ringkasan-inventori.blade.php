<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Ringkasan Umum</h1>
        <div class="flex gap-2 items-center">
            <button type="button" wire:click="cetakInformasi"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none bg-gray-600 text-white hover:bg-gray-700 active:bg-gray-900 transition ease-in-out duration-150">
                Cetak Informasi
            </button>
        </div>
    </div>
    <div class="flex items-center mr-4 gap-2 my-8">
        @can('Kasir')
        <div class="cursor-pointer">
            <a href="{{ route('ringkasan-kasir') }}"
                class="text-gray-800 bg-gray-200 rounded-xl py-2 px-4 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                Kasir
            </a>
        </div>
        @endcan
        @can('Produksi')

        <div class="cursor-pointer">
            <a href="{{ route('ringkasan-produksi') }}"
                class="bg-gray-200 text-gray-800 rounded-xl py-2 px-4 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                Produksi
            </a>
        </div>
        @endcan
        @can('Inventori')

        <div class="cursor-pointer">
            <a href="{{ route('ringkasan-inventori') }}"
                class="text-gray-100 bg-gray-800 rounded-xl py-2 px-4 border border-gray-600 hover:text-gray-100 hover:bg-gray-800 transition-colors size-8">
                Inventori
            </a>
        </div>
        @endcan

    </div>
    <div class="flex flex-col overflow-hidden  bg-white rounded-lg shadow p-4 md:flex-row gap-6" style="height: 26rem;">
        <!-- Kalender -->
        <div class="bg-white border border-gray-500 max-h-96 rounded-lg shadow p-4 w-full md:w-1/3">
            <h2 class="text-lg font-semibold mb-4">Kalender Pawon3D</h2>

            <!-- Navigasi Bulan -->
            <div class="flex items-center justify-between mb-4">
                <flux:button type="button" icon="chevron-left" wire:click="previousMonth"
                    class="text-gray-500 hover:text-black" />
                <div class="font-semibold text-center">
                    {{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F Y') }}
                </div>
                <flux:button type="button" icon="chevron-right" wire:click="nextMonth"
                    class="text-gray-500 hover:text-black" />
            </div>

            <!-- Hari -->
            <div class="grid grid-cols-7 text-center text-sm text-gray-500 font-semibold mb-2">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                <div @class([ $day==='Min' || $day==='Sab' ? 'text-red-500' : 'text-gray-500' , ])>{{ $day }}</div>
                @endforeach
            </div>

            <!-- Tanggal -->
            <div class="grid grid-cols-7 gap-1 text-center text-sm">
                @foreach ($calendar as $date => $info)
                @php
                $carbonDate = \Carbon\Carbon::parse($date);
                $dayOfWeek = $carbonDate->translatedFormat('D');
                $isWeekend = in_array($dayOfWeek, ['Min', 'Sab']);
                $hasExpense = $info['hasExpense'] ?? false;
                @endphp
                <div class="flex flex-col items-center">
                    <button wire:click="selectDate('{{ $date }}')" class="rounded-lg w-8 h-8
            {{ $selectedDate === $date ? 'bg-black text-white' : ($info['isCurrentMonth'] ? ($isWeekend ? 'text-red-500' : 'text-black') : ($isWeekend ? 'text-red-300' : 'text-gray-400')) }}
            hover:bg-gray-200">
                        {{ $carbonDate->day }}
                    </button>
                    @if ($hasExpense)
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mt-1"></span>
                    @endif
                </div>
                @endforeach
            </div>

        </div>

        <!-- Detail Transaksi -->
        <div class="w-full md:w-2/3 overflow-y-auto scroll-hide">
            <h2 class="text-lg font-semibold mb-2">
                {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F') }}
            </h2>

            <!-- Hari Ini -->
            <div class="space-y-3">
                @forelse($todayExpenses as $exp)
                <div class="bg-white border rounded-lg shadow p-4 flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-500 flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-black"></span>
                            {{ \Carbon\Carbon::parse($exp->expense_date)->format('d F Y') }}
                        </p>
                        <p class="font-semibold mt-1">
                            {{ strtoupper($exp->expense_number) }} ( {{ $exp->supplier->name }} )
                        </p>
                        <p class="text-sm text-gray-500 capitalize">{{ str_replace('-', ' ', $exp->method) }}</p>
                    </div>
                    <flux:button icon="chevron-right" type="button" variant="ghost"
                        href="{{ route('belanja.rincian', $exp->id) }}" class="text-gray-500 hover:text-black" />
                </div>
                @empty
                <p class="text-sm text-gray-500">Tidak ada belanja pada hari ini.</p>
                @endforelse
            </div>

            <!-- Lainnya -->
            @if($otherExpenses->count())
            <div class="mt-6">
                <h3 class="text-sm font-semibold mb-2 text-gray-600">Lainnya</h3>
                <div class="space-y-3">
                    @foreach ($otherExpenses as $exp)
                    <div class="bg-white border rounded-lg shadow p-4 flex items-start justify-between">
                        <div>
                            <p class="text-xs text-gray-500 flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-black"></span>
                                {{ \Carbon\Carbon::parse($exp->expense_date)->format('d F Y') }}
                            </p>
                            <p class="font-semibold mt-1">
                                {{ strtoupper($exp->expense_number) }} ( {{ $exp->supplier->name }} )
                            </p>
                            <p class="text-sm text-gray-500 capitalize">Belanja Persediaan</p>
                        </div>
                        <flux:button icon="chevron-right" type="button" variant="ghost"
                            href="{{ route('belanja.rincian', $exp->id) }}" class="text-gray-500 hover:text-black" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white mt-8 rounded-lg shadow p-4">
        <div class="flex flex-row justify-between items-center">
            <h2 class="text-lg font-normal">Belanja Persediaan</h2>
        </div>

        @if ($expenses->isEmpty())
        <div class="col-span-5 text-center bg-gray-300 p-4 rounded-2xl flex flex-col items-center justify-center mt-8">
            <p class="text-gray-700 font-semibold">Belum ada belanja.</p>
        </div>
        @else
        <div class="bg-white rounded-xl border mt-8">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_number')">
                                Nomor Belanja
                                {{ $sortDirection === 'asc' && $sortField === 'expense_number' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_date')">
                                Tanggal Belanja
                                {{ $sortDirection === 'asc' && $sortField === 'expense_date' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('supplier_name')">
                                Toko Persediaan
                                {{ $sortDirection === 'asc' && $sortField === 'supplier_name' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('status')">
                                Status
                                {{ $sortDirection === 'asc' && $sortField === 'status' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Barang Didapatkan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('grand_total_expect')">
                                Total Harga (Perkiraan)
                                {{ $sortDirection === 'asc' && $sortField === 'grand_total_expect' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('grand_total_actual')">
                                Total Harga (Sebenarnya)
                                {{ $sortDirection === 'asc' && $sortField === 'grand_total_actual' ? '↑' : '↓' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($expenses as $expense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('belanja.rincian', $expense->id) }}"
                                    class="hover:bg-gray-50 cursor-pointer">
                                    {{ $expense->expense_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $expense->expense_date ?
                                \Carbon\Carbon::parse($expense->expense_date)->format('d-m-Y')
                                :
                                '-' }}
                            </td>
                            <td class="px-6 py-4 text-left whitespace-nowrap">
                                {{ $expense->supplier->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-left whitespace-nowrap">
                                {{ $expense->status ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-left whitespace-nowrap">
                                {{-- <div class="flex items-center space-x-2 flex-col">
                                    <div class="w-full h-4 mb-4 bg-gray-200 rounded-full dark:bg-gray-700">
                                        <div class="h-4 bg-blue-600 rounded-full dark:bg-blue-500"
                                            style="width: {{ number_format($expense->expenseDetails->where('is_quantity_get', true)->count() / $expense->expenseDetails->count() * 100, 0) }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $expense->expenseDetails->where('is_quantity_get', true)->count() ?? '0' }}
                                        dari
                                        {{ $expense->expenseDetails->count() ?? '0' }} barang
                                    </span>
                                </div> --}}
                                @php
                                $total_expect = $expense->expenseDetails->sum('quantity_expect');
                                $total_get = $expense->expenseDetails->sum('quantity_get');
                                $percentage = $total_expect > 0 ? ($total_get / $total_expect) * 100 : 0;
                                @endphp

                                <div class="flex items-center space-x-2 flex-col">
                                    <div class="w-full h-4 mb-4 bg-gray-200 rounded-full dark:bg-gray-700">
                                        <div class="h-4 bg-blue-600 rounded-full dark:bg-blue-500"
                                            style="width: {{ number_format($percentage, 0) }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ number_format($percentage, 0) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-left whitespace-nowrap">
                                Rp{{ number_format($expense->grand_total_expect, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-left space-x-2 whitespace-nowrap">
                                Rp{{ number_format($expense->grand_total_actual, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $expenses->links() }}
            </div>
        </div>
        @endif
    </div>

    <div class="bg-white mt-8 rounded-lg shadow p-4">
        <div class="flex flex-row justify-between items-center">
            <h2 class="text-lg font-normal">Persediaan Rendah</h2>
        </div>
        <div class="bg-white rounded-xl border mt-8">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_number')">
                                Barang
                                {{ $sortDirection === 'asc' && $sortField === 'expense_number' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_date')">
                                Jumlah
                                {{ $sortDirection === 'asc' && $sortField === 'expense_date' ? '↑' : '↓' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($materials as $material)
                        <tr class="hover:bg-gray-100 cursor-pointer"
                            wire:click="redirectToMaterial('{{ $material->id }}')">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $material->name }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
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

                                // Ambil satu detail utama untuk unit (jika ada)
                                $mainDetail = \App\Models\MaterialDetail::where('material_id', $material->id)
                                ->where('is_main', true)
                                ->first();
                                @endphp
                                {{ $batches->isNotEmpty() ? $quantity_main_total . ' ' . ($mainDetail->unit->alias
                                ?? '') : 'Tidak Tersedia' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{-- {{ $expenses->links() }} --}}
            </div>
        </div>
    </div>

    <div class="bg-white mt-8 rounded-lg shadow p-4">
        <div class="flex flex-row justify-between items-center">
            <h2 class="text-lg font-normal">Persediaan Hampir dan Telah Expired</h2>
        </div>
        <div class="bg-white rounded-xl border mt-8">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_number')">
                                Barang
                                {{ $sortDirection === 'asc' && $sortField === 'expense_number' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_number')">
                                Batch
                                {{ $sortDirection === 'asc' && $sortField === 'expense_number' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_number')">
                                Tanggal Expired
                                {{ $sortDirection === 'asc' && $sortField === 'expense_number' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_number')">
                                Status
                                {{ $sortDirection === 'asc' && $sortField === 'expense_number' ? '↑' : '↓' }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('expense_date')">
                                Jumlah
                                {{ $sortDirection === 'asc' && $sortField === 'expense_date' ? '↑' : '↓' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($materialB as $material)
                        @php
                        $batches = $material->batches;

                        $today = now();
                        $nearFuture = now()->addMonth();

                        // Ambil batch yang expired atau hampir expired (kurang dari 1 bulan dari sekarang)
                        $filteredBatches = $batches->filter(function ($batch) use ($today, $nearFuture) {
                        $date = \Carbon\Carbon::parse($batch->date);
                        return $date->lessThanOrEqualTo($nearFuture);
                        });
                        @endphp

                        @foreach ($filteredBatches as $batch)
                        <tr class="hover:bg-gray-100 cursor-pointer"
                            wire:click="redirectToMaterial('{{ $material->id }}')">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $material->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $batch->batch_number ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($batch->date)->translatedFormat('j F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(\Carbon\Carbon::parse($batch->date)->isPast())
                                <span class="text-red-600 font-semibold">Expired</span>
                                @else
                                <span class="text-yellow-600 font-medium">Hampir Expired</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                {{ $batch->batch_quantity }} {{ $batch->unit->alias ?? '' }}
                            </td>
                        </tr>
                        @endforeach
                        @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{-- {{ $expenses->links() }} --}}
            </div>
        </div>
    </div>
</div>
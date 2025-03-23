<?php

use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\View;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    public string $search_code = '';

    public function mount()
    {
        View::share('title', 'Kode Hadiah Yang Sudah Didapat');
    }

    public function with() : array {
        return [
            'prizes' => \App\Models\Prize::where('is_get', true)->when($this->search_code, function ($query) {
                return $query->where('code', 'like', '%' . $this->search_code . '%');
            })->with('product')->paginate(5)
        ];
    }

}; ?>

<section class="w-full">
    <x-prize.layout :heading="__('Kode Hadiah Yang Sudah Didapat')">


        <div class="bg-white rounded-xl border">
            <!-- Search Input -->
            <div class="p-4">
                <input wire:model.live="search_code" placeholder="Cari..."
                    class="w-full max-w-sm px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode Hadiah</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk Hadiah</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal Didapat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($prizes as $prize)
                        <tr>
                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ $prize->code }}</td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">{{ $prize->product->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                {{ Carbon\Carbon::parse($prize->get_at)->format('d M Y') ?? '-' }} ({{
                                Carbon\Carbon::parse($prize->get_at)->diffForHumans() }})
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $prizes->links() }}
            </div>
        </div>

    </x-prize.layout>
</section>
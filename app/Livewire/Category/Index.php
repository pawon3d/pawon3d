<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('categories')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Kategori');
    }


    public function render()
    {
        $categories = Category::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })->with('products')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->paginate(10);

        return view('livewire.category.index', compact('categories'));
    }

    public function cetakInformasi()
    {
        return redirect()->route('kategori.pdf', [
            'search' => $this->search,
        ]);
    }
}
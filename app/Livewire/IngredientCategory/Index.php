<?php

namespace App\Livewire\IngredientCategory;

use App\Models\IngredientCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\View;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination;
    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $filterStatus = '';

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('ingredient_categories')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function mount()
    {
        View::share('title', 'Kategori Persediaan');
    }
    public function render()
    {
        $categories = IngredientCategory::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->when($this->filterStatus, function ($query) {
                return $query->where('is_active', $this->filterStatus === 'aktif');
            })
            ->paginate(10);
        return view('livewire.ingredient-category.index', compact('categories'));
    }

    public function cetakInformasi()
    {
        return redirect()->route('kategori-persediaan.pdf', [
            'search' => $this->search,
        ]);
    }
}
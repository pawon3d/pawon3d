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
    public $sortField = 'name';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'filterStatus',  'sortField', 'sortDirection'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

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
            })->with('details')->withCount('details')
            ->orderBy($this->sortField, $this->sortDirection)
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

<?php

namespace App\Livewire\Material;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Material;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination, WithFileUploads, LivewireAlert;

    public $activityLogs = [];
    public $filterStatus = '';
    public $search = '';
    public $showHistoryModal = false;
    public $viewMode = 'grid';

    protected $queryString = ['viewMode', 'filterStatus', 'search'];

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('materials')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('bahan-baku.pdf', [
            'search' => $this->search,
        ]);
    }

    public function updatedViewMode($value)
    {
        session()->put('viewMode', $value);
    }
    public function mount()
    {
        View::share('mainTitle', 'Inventori');
        View::share('title', 'Bahan Baku');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }


    public function render()
    {
        $materials = Material::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })->when($this->filterStatus, function ($query) {
            return $query->where('is_active', $this->filterStatus === 'aktif');
        })->with('material_details')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.material.index', compact('materials'));
    }
}
<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert;
    public $search = '';
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $filterStatus = '';

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('users')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function cetakInformasi()
    {
        return redirect()->route('user.pdf', [
            'search' => $this->search,
        ]);
    }

    public function mount()
    {
        View::share('title', 'Pekerja');
        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
    }


    public function render()
    {
        $users = User::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%');
        })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.user.index', compact('users'));
    }
}
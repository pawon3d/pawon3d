<?php

namespace App\Livewire\Category;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Tambah extends Component
{
    public $name, $is_active = false;
    protected $rules = [
        'name' => 'required|min:3|unique:categories,name',
    ];

    protected $messages = [
        'name.required' => 'Nama kategori tidak boleh kosong',
        'name.min' => 'Nama kategori minimal 3 karakter',
        'name.unique' => 'Nama kategori sudah ada',
    ];

    public function mount()
    {
        View::share('title', 'Tambah Kategori');
        View::share('mainTitle', 'Inventori');
    }

    public function store()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        return redirect()->intended(route('kategori'));
    }

    public function resetForm()
    {
        $this->name = '';
        $this->is_active = false;
    }

    public function render()
    {
        return view('livewire.category.tambah');
    }
}

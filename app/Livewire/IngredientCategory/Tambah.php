<?php

namespace App\Livewire\IngredientCategory;

use App\Models\IngredientCategory;
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
        View::share('mainTitle', 'Inventori');
        View::share('title', 'Tambah Kategori Persediaan');
    }

    public function store()
    {
        $this->validate();

        IngredientCategory::create([
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        $this->resetForm();
        return redirect()->intended(route('kategori-persediaan'));
    }

    public function resetForm()
    {
        $this->name = '';
        $this->is_active = false;
    }
    public function render()
    {
        return view('livewire.ingredient-category.tambah');
    }
}

<?php

namespace App\Livewire\Material;

use App\Models\IngredientCategoryDetail;
use App\Models\Unit;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class Tambah extends Component
{
    use \Livewire\WithFileUploads;

    public $name;

    public $description;

    public $expiry_date = '00/00/0000';

    public $status = 'Kosong';

    public $category_ids;

    public $minimum = 0;

    public $is_active = false;

    public $is_recipe = false;

    public $main_unit_id;

    public $main_unit_alias;

    public $main_unit_name;

    public $previewImage;

    public $image;

    public $material_details = [];

    public $ingredient_category_details = [];

    public $supply_quantity_main = [];

    public $supply_price_total = [];

    protected $messages = [
        'name.required' => 'Nama bahan baku tidak boleh kosong.',
        'image.image' => 'File yang diunggah harus berupa gambar.',
        'image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        'image.mimes' => 'Format gambar yang diizinkan adalah jpg, jpeg, png.',
    ];

    public function mount(): void
    {
        \Illuminate\Support\Facades\View::share('title', 'Tambah Bahan Baku');
        View::share('mainTitle', 'Inventori');

        $this->material_details = [[
            'unit_id' => '',
            'quantity' => 1,
        ]];
    }

    public function updatedImage(): void
    {
        $this->validate([
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $this->previewImage = $this->image->temporaryUrl();
    }

    public function removeImage(): void
    {
        $this->reset('image', 'previewImage');
    }

    public function addUnit(): void
    {
        $this->material_details[] = [
            'unit_id' => '',
            'quantity' => 0,
            'supply_quantity' => 0,
            'supply_price' => 0,
            'is_main' => false,
        ];
    }

    public function removeUnit($index): void
    {
        if (count($this->material_details) > 1) {
            unset($this->material_details[$index]);
            $this->material_details = array_values($this->material_details);
        }
    }

    public function setUnit($index, $unitId): void
    {
        if (! $unitId) {
            return;
        }

        $unit = Unit::find($unitId);

        if (! $unit) {
            return;
        }

        if ($index == 0) {
            $this->main_unit_id = $unitId;
            $this->main_unit_alias = $unit->alias;
            $this->main_unit_name = $unit->name;
            $this->material_details[$index]['is_main'] = true;
        }

        $this->material_details[$index]['unit_id'] = $unitId;
        $this->material_details[$index]['unit'] = $unit->alias;
        $this->material_details[$index]['unit_name'] = $unit->name;
    }

    public function updatedMaterialDetails(): void
    {
        $this->material_details = array_map(function ($detail) {
            return [
                'unit_id' => $detail['unit_id'] ?? null,
                'unit' => $detail['unit'] ?? null,
                'unit_name' => $detail['unit_name'] ?? null,
                'quantity' => $detail['quantity'] ?? 0,
                'is_main' => $detail['is_main'] ?? false,
                'supply_quantity' => $detail['supply_quantity'] ?? 0,
                'supply_price' => $detail['supply_price'] ?? 0,
            ];
        }, $this->material_details);
        $this->supply_quantity_main = collect($this->material_details)
            ->sum(function ($detail) {
                return $detail['quantity'] ?? 0;
            });
        $this->supply_price_total = collect($this->material_details)
            ->sum(function ($detail) {
                return ($detail['supply_price'] ?? 0) * ($detail['supply_quantity'] ?? 0);
            });
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'status' => 'nullable|string|max:20',
            'minimum' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_recipe' => 'boolean',
            'material_details.*.unit_id' => 'nullable|exists:units,id',
            'material_details.*.quantity' => 'nullable|numeric|min:0',
            'material_details.*.is_main' => 'boolean',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
            $material = \App\Models\Material::create([
                'name' => $this->name,
                'description' => $this->description,
                'expiry_date' => null,
                'status' => $this->status,
                'minimum' => $this->minimum,
                'is_active' => $this->is_active,
                'is_recipe' => $this->is_recipe,
                'image' => $this->image ? $this->image->store('material_images', 'public') : null,
            ]);

            // Create category relationships
            if ($this->category_ids) {
                foreach ($this->category_ids as $category_id) {
                    IngredientCategoryDetail::create([
                        'material_id' => $material->id,
                        'ingredient_category_id' => $category_id,
                    ]);
                }
            }

            // Create material details
            if (! empty($this->material_details[0]['unit_id'])) {
                $detailsData = collect($this->material_details)->map(fn ($detail) => [
                    'unit_id' => $detail['unit_id'] ?? null,
                    'quantity' => $detail['quantity'] ?? 0,
                    'is_main' => $detail['is_main'] ?? false,
                ])->toArray();

                $material->material_details()->createMany($detailsData);
            }
        });

        return redirect()->intended(route('bahan-baku'))
            ->with('success', 'Bahan Baku berhasil ditambahkan.');
    }

    public function render()
    {
        return view('livewire.material.tambah', [
            'categories' => \App\Models\IngredientCategory::lazy(),
            'units' => \App\Models\Unit::lazy(),
        ]);
    }
}

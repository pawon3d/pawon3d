<?php

namespace App\Livewire\Material;

use App\Models\IngredientCategoryDetail;
use App\Models\Material;
use App\Models\Unit;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Form extends Component
{
    use \Livewire\WithFileUploads, \Jantinnerezo\LivewireAlert\LivewireAlert;

    // Material ID (null for create, has value for edit)
    public $material_id;

    // Form fields
    public $name, $description, $expiry_date = '00/00/0000', $status = 'Kosong', $category_ids, $minimum = 0;
    public $is_active = false, $is_recipe = false;

    // Unit related
    public $main_unit_id, $main_unit_alias, $main_unit_name, $main_supply_quantity = 0;

    // Image
    public $previewImage, $image;

    // Details
    public $material_details = [];
    public $ingredient_category_details = [];

    // Calculations
    public $supply_quantity_main = 0, $supply_quantity_total = 0, $supply_quantity_modal = 0;
    public $supply_price_total = 0;
    public $quantity_main, $quantity_main_total;

    // UI State
    public $showHistoryModal = false;
    public $activityLogs = [];
    public $material;

    protected $listeners = [
        'delete',
        'recalculateSupplies',
    ];

    protected $messages = [
        'name.required' => 'Nama bahan baku tidak boleh kosong.',
        'image.image' => 'File yang diunggah harus berupa gambar.',
        'image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        'image.mimes' => 'Format gambar yang diizinkan adalah jpg, jpeg, png.',
    ];

    public function mount($id = null): void
    {
        $this->material_id = $id;

        if ($id) {
            // Edit mode
            View::share('title', 'Rincian Bahan Baku');
            $this->loadMaterial($id);
        } else {
            // Create mode
            View::share('title', 'Tambah Bahan Baku');
            $this->material_details = [[
                'unit_id' => '',
                'quantity' => 1,
            ]];
        }

        View::share('mainTitle', 'Inventori');
    }

    protected function loadMaterial($id): void
    {
        $material = Material::with(['material_details.unit', 'ingredientCategoryDetails'])
            ->findOrFail($id);

        $this->material = $material;
        $this->name = $material->name;
        $this->description = $material->description;
        $this->expiry_date = $material->expiry_date ? \Carbon\Carbon::parse($material->expiry_date)->format('d/m/Y') : '00/00/0000';
        $this->status = $material->status;
        $this->minimum = $material->minimum;
        $this->is_active = $material->is_active ?? false;
        $this->is_recipe = $material->is_recipe ?? false;
        $this->category_ids = $material->ingredientCategoryDetails->pluck('ingredient_category_id')->toArray();

        // Load material details
        if ($material->material_details->isEmpty()) {
            $this->material_details = [[
                'unit_id' => '',
                'quantity' => 1,
            ]];
        } else {
            $firstDetail = $material->material_details->first();
            $this->main_unit_id = $firstDetail->unit_id ?? null;
            $this->main_unit_alias = $firstDetail->unit->alias ?? '';
            $this->main_unit_name = $firstDetail->unit->name ?? '';
            $this->main_supply_quantity = $firstDetail->supply_quantity ?? 0;

            $this->material_details = $material->material_details->map(function ($detail) {
                return [
                    'unit_id' => $detail->unit_id,
                    'unit_name' => $detail->unit->name,
                    'unit' => $detail->unit->alias,
                    'quantity' => $detail->quantity,
                    'supply_quantity' => $detail->supply_quantity,
                    'supply_price' => $detail->supply_price,
                    'is_main' => $detail->is_main,
                ];
            })->toArray();

            // Calculate totals
            $this->calculateTotals();
        }

        // Set preview image
        $this->previewImage = $material->image ? env('APP_URL') . '/storage/' . $material->image : null;
    }

    protected function calculateTotals(): void
    {
        $details = collect($this->material_details);
        $this->supply_quantity_main = $details->sum('quantity');
        $this->supply_price_total = $details->sum(fn($d) => ($d['supply_price'] ?? 0) * ($d['supply_quantity'] ?? 0));
        $this->supply_quantity_total = $details->sum(fn($d) => (max(1, $d['supply_quantity'] ?? 0) * ($d['quantity'] ?? 0)));
        $this->supply_quantity_modal = $details->sum(fn($d) => (($d['supply_quantity'] ?? 0) * ($d['quantity'] ?? 0)));
    }

    public function riwayatPembaruan(): void
    {
        $this->activityLogs = Activity::inLog('materials')->where('subject_id', $this->material_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
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
        if (!$unitId) {
            return;
        }

        $unit = Unit::find($unitId);

        if (!$unit) {
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

        $this->calculateTotals();
    }

    public function confirmDelete(): void
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus bahan ini?', [
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'onCancelled' => 'cancelled',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {
        $material = Material::find($this->material_id);

        if (!$material) {
            return redirect()->intended(route('bahan-baku'))->with('error', 'Bahan tidak ditemukan.');
        }

        DB::transaction(function () use ($material) {
            // Hapus gambar jika ada
            if ($material->image && Storage::disk('public')->exists($material->image)) {
                Storage::disk('public')->delete($material->image);
            }

            $material->delete();
        });

        return redirect()->intended(route('bahan-baku'))->with('success', 'Bahan berhasil dihapus.');
    }

    public function save()
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

        DB::transaction(function () {
            if ($this->material_id) {
                $this->updateMaterial();
            } else {
                $this->createMaterial();
            }
        });

        return redirect()->intended(route('bahan-baku'))
            ->with('success', 'Bahan Baku berhasil ' . ($this->material_id ? 'diperbarui' : 'ditambahkan') . '.');
    }

    protected function createMaterial(): void
    {
        $material = Material::create([
            'name' => $this->name,
            'description' => $this->description,
            'expiry_date' => null,
            'status' => $this->status,
            'minimum' => $this->minimum,
            'is_active' => $this->is_active,
            'is_recipe' => $this->is_recipe,
            'image' => $this->image ? $this->image->store('material_images', 'public') : null,
        ]);

        $this->saveCategoryRelationships($material);
        $this->saveMaterialDetails($material);
    }

    protected function updateMaterial(): void
    {
        $material = Material::with('batches')->findOrFail($this->material_id);

        // Calculate status based on batches
        $hasExpiredBatch = $material->batches->contains(fn($batch) => $batch->date < now()->format('Y-m-d'));
        $totalQuantity = $material->batches->sum('batch_quantity');

        if ($hasExpiredBatch) {
            $status = 'Expired';
        } elseif ($totalQuantity <= 0) {
            $status = 'Kosong';
        } elseif ($totalQuantity <= $this->minimum) {
            $status = 'Habis';
        } elseif ($totalQuantity > $this->minimum * 2) {
            $status = 'Tersedia';
        } else {
            $status = 'Hampir Habis';
        }

        // Handle image upload
        $imageData = [];
        if ($this->image) {
            // Delete old image
            if ($material->image && Storage::disk('public')->exists($material->image)) {
                Storage::disk('public')->delete($material->image);
            }
            $imageData['image'] = $this->image->store('material_images', 'public');
        }

        // Update material
        $material->update(array_merge([
            'name' => $this->name,
            'description' => $this->description,
            'status' => $status,
            'minimum' => $this->minimum,
            'is_active' => $this->is_active,
            'is_recipe' => $this->is_recipe,
        ], $imageData));

        // Update relationships
        $material->ingredientCategoryDetails()->delete();
        $material->material_details()->delete();

        $this->saveCategoryRelationships($material);
        $this->saveMaterialDetails($material);
    }

    protected function saveCategoryRelationships($material): void
    {
        if ($this->category_ids) {
            foreach ($this->category_ids as $category_id) {
                IngredientCategoryDetail::create([
                    'material_id' => $material->id,
                    'ingredient_category_id' => $category_id,
                ]);
            }
        }
    }

    protected function saveMaterialDetails($material): void
    {
        if (!empty($this->material_details[0]['unit_id'])) {
            $detailsData = collect($this->material_details)->map(fn($detail) => [
                'unit_id' => $detail['unit_id'] ?? null,
                'quantity' => $detail['quantity'] ?? 0,
                'is_main' => $detail['is_main'] ?? false,
                'supply_quantity' => $detail['supply_quantity'] ?? 0,
                'supply_price' => $detail['supply_price'] ?? 0,
            ])->toArray();

            $material->material_details()->createMany($detailsData);
        }
    }

    public function render()
    {
        return view('livewire.material.form', [
            'categories' => \App\Models\IngredientCategory::lazy(),
            'units' => \App\Models\Unit::lazy(),
        ]);
    }
}

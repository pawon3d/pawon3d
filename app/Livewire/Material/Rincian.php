<?php

namespace App\Livewire\Material;

use App\Models\IngredientCategoryDetail;
use App\Models\Material;
use App\Models\Unit;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Livewire\WithFileUploads, \Jantinnerezo\LivewireAlert\LivewireAlert;

    public $material_id;
    public $name, $description, $expiry_date = '00-00-0000', $status = 'kosong', $category_ids, $minimum = 0, $is_active = false;
    public $main_unit_id, $main_unit_alias, $main_unit_name;
    public $previewImage;
    public $image;
    public $material_details = [];
    public $ingredient_category_details = [];
    public $supply_quantity_main;
    public $supply_price_total;
    public $showHistoryModal = false;
    public $activityLogs = [];

    protected $listeners = [
        'delete',
    ];

    protected $messages = [
        'name.required' => 'Nama bahan baku tidak boleh kosong.',
        'image.image' => 'File yang diunggah harus berupa gambar.',
        'image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        'image.mimes' => 'Format gambar yang diizinkan adalah jpg, jpeg, png.',
    ];

    public function mount($id)
    {
        \Illuminate\Support\Facades\View::share('title', 'Rincian Bahan Baku');
        $this->material_id = $id;
        $material = \App\Models\Material::findOrFail($id);
        $this->main_unit_id = $material->material_details->first()->unit_id ?? null;
        $this->main_unit_alias = $material->material_details->first()->unit->alias ?? '';
        $this->main_unit_name = $material->material_details->first()->unit->name ?? '';
        $this->name = $material->name;
        $this->description = $material->description;
        $this->expiry_date = $material->expiry_date ? $material->expiry_date->format('d/m/Y') : '00/00/0000';
        $this->status = $material->status;
        $this->minimum = $material->minimum;
        $this->is_active = $material->is_active;
        $this->category_ids = $material->ingredientCategoryDetails->pluck('ingredient_category_id')->toArray();
        if ($material->material_details->isEmpty()) {
            $this->material_details = [[
                'unit_id' => '',
                'quantity' => 1,
            ]];
        } else {

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

            $this->supply_quantity_main = collect($this->material_details)
                ->sum(function ($detail) {
                    return ($detail['supply_quantity'] ?? 0) * ($detail['quantity'] ?? 0);
                });
            $this->supply_price_total = collect($this->material_details)
                ->sum(function ($detail) {
                    return ($detail['supply_price'] ?? 0) * ($detail['supply_quantity'] ?? 0);
                });
        }

        if ($material->image) {
            $this->previewImage = env('APP_URL') . '/storage/' . $material->image;
        } else {
            $this->previewImage = null;
        }
    }


    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('materials')->where('subject_id', $this->material_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $this->previewImage = $this->image->temporaryUrl();
    }
    public function removeImage()
    {
        $this->reset('image', 'previewImage');
    }

    public function addUnit()
    {
        $this->material_details[] = [
            'unit_id' => '',
            'quantity' => 0,
            'supply_quantity' => 0,
            'supply_price' => 0,
            'is_main' => false,
        ];
    }
    public function removeUnit($index)
    {
        if (count($this->material_details) > 1) {
            unset($this->material_details[$index]);
            $this->material_details = array_values($this->material_details);
        }
    }

    public function setUnit($index, $unitId)
    {
        if ($index == 0) {
            $this->main_unit_id = $unitId;
            $this->main_unit_alias = Unit::find($unitId)->alias;
            $this->main_unit_name = Unit::find($unitId)->name;
            $this->material_details[$index]['is_main'] = true;
        }
        $this->material_details[$index]['unit_id'] = $unitId;

        if ($unitId) {
            $unit = Unit::find($unitId);
            $this->material_details[$index]['unit'] = $unit->alias;
            $this->material_details[$index]['unit_name'] = $unit->name;
        }
    }

    public function updatedMaterialDetails()
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
                return ($detail['supply_quantity'] ?? 0) * ($detail['quantity'] ?? 0);
            });
        $this->supply_price_total = collect($this->material_details)
            ->sum(function ($detail) {
                return ($detail['supply_price'] ?? 0) * ($detail['supply_quantity'] ?? 0);
            });
    }

    public function confirmDelete()
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

        if ($material) {
            $material->delete();
            // Hapus gambar produk jika ada
            if ($material->image) {
                $oldImagePath = public_path('storage/' . $material->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            return redirect()->intended(route('bahan-baku'))->with('success', 'Bahan berhasil dihapus.');
        }
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'status' => 'nullable|string|max:20',
            'minimum' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'material_details.*.unit_id' => 'nullable|exists:units,id',
            'material_details.*.quantity' => 'nullable|numeric|min:0',
            'material_details.*.is_main' => 'boolean',
        ]);
        $material = \App\Models\Material::findOrFail($this->material_id);
        $material->update([
            'name' => $this->name,
            'description' => $this->description,
            'expiry_date' => null,
            'status' => $this->status,
            'minimum' => $this->minimum,
            'is_active' => $this->is_active,
        ]);

        if ($this->image) {
            // Hapus gambar lama jika ada
            if ($material->image) {
                $oldImagePath = public_path('storage/' . $material->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $material->image = $this->image->store('material_images', 'public');
            $material->save();
        }

        if ($this->category_ids) {
            // Hapus kategori lama
            $material->ingredientCategoryDetails()->delete();
            // Tambahkan kategori baru
            foreach ($this->category_ids as $category_id) {
                IngredientCategoryDetail::create([
                    'material_id' => $material->id,
                    'ingredient_category_id' => $category_id,
                ]);
            }
        }

        if ($this->material_details[0]['unit_id'] != null && $this->material_details[0]['unit_id'] != '') {
            // Hapus detail lama
            $material->material_details()->delete();
            // Tambahkan detail baru
            foreach ($this->material_details as $detail) {
                $cleanData = [
                    'unit_id' => $detail['unit_id'] ?? null,
                    'quantity' => $detail['quantity'] ?? 0,
                    'is_main' => $detail['is_main'] ?? false,
                ];

                $material->material_details()->create($cleanData);
            }
        }

        return redirect()->intended(route('bahan-baku'))
            ->with('success', 'Bahan Baku berhasil diperbarui.');
    }
    public function render()
    {
        return view('livewire.material.rincian', [
            'categories' => \App\Models\IngredientCategory::lazy(),
            'units' => \App\Models\Unit::lazy(),
        ]);
    }
}
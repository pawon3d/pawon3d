<?php

namespace App\Livewire\Product;

use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;

class Rincian extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, WithFileUploads;

    public $product_image;

    public $name;

    public $description;

    public $is_recipe = false;

    public $is_active = false;

    public $is_recommended = false;

    public $is_other = false;

    public $pcs = 1;

    public $capital = 0;

    public $pcs_capital = 0;

    public $product_compositions = [];

    public $other_costs = [];

    public $category_ids = [];

    public $price = 0;

    public $total = 0;

    public $stock = 0;

    public $product_id;

    public $previewImage = null;

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $selectedMethods = [];

    public $suhu_ruangan = 0;

    public $suhu_dingin = 0;

    public $suhu_beku = 0;

    protected $listeners = [
        'delete',
        'updatePreview',
    ];

    protected array $rules = [
        'price' => 'nullable|numeric|min:0',
    ];

    protected $messages = [
        'name.required' => 'Nama produk tidak boleh kosong.',
        'product_image.image' => 'File yang diunggah harus berupa gambar.',
        'product_image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        'product_image.mimes' => 'Format gambar yang diizinkan adalah jpg, jpeg, png.',
    ];

    public function mount($id)
    {
        View::share('title', 'Rincian Produk');
        View::share('mainTitle', 'Inventori');

        $this->product_id = $id;
        $product = Product::with('product_compositions', 'product_categories', 'other_costs')->findOrFail($this->product_id);
        $this->name = $product->name;
        $this->description = $product->description;
        $this->category_ids = $product->product_categories->pluck('category_id')->toArray();
        $this->is_recipe = $product->is_recipe;
        $this->is_active = $product->is_active;
        $this->is_recommended = $product->is_recommended;
        $this->is_other = $product->is_other;
        $this->pcs = $product->pcs;
        $this->capital = $product->capital;
        $this->pcs_capital = $product->pcs_capital;
        $this->suhu_ruangan = $product->suhu_ruangan;
        $this->suhu_dingin = $product->suhu_dingin;
        $this->suhu_beku = $product->suhu_beku;
        $this->product_compositions = $product->product_compositions->map(function ($composition) {
            $materialDetail = MaterialDetail::where('material_id', $composition->material_id)
                ->where('unit_id', $composition->unit_id)
                ->first();
            if ($materialDetail) {
                $composition->material_price = $materialDetail->supply_price;
            } else {
                $composition->material_price = 0;
            }

            // Mengembalikan data komposisi dengan harga material
            return [
                'material_id' => $composition->material_id,
                'material_quantity' => $composition->material_quantity,
                'unit_id' => $composition->unit_id,
                'material_price' => $composition->material_price,
            ];
        })->toArray();
        if (empty($this->product_compositions)) {
            $this->product_compositions = [[
                'material_id' => '',
                'material_quantity' => 0,
                'unit_id' => '',
                'material_price' => 0,
            ]];
        }
        $this->other_costs = $product->other_costs->map(function ($cost) {
            return [
                'type_cost_id' => $cost->type_cost_id,
                'name' => $cost->name,
                'price' => $cost->price,
            ];
        })->toArray();
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->selectedMethods = $product->method ? $product->method : [];
        if ($product->product_image) {
            $this->previewImage = env('APP_URL').'/storage/'.$product->product_image;
        } else {
            $this->previewImage = null;
        }
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('products')->where('subject_id', $this->product_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function updatedIsRecipe($value)
    {
        $this->resetRecipeState((bool) $value);
    }

    public function addComposition()
    {
        $this->product_compositions[] = $this->defaultCompositionRow();
    }

    public function removeComposition($index)
    {
        unset($this->product_compositions[$index]);
        $this->product_compositions = array_values($this->product_compositions);

        if (empty($this->product_compositions)) {
            $this->product_compositions[] = $this->defaultCompositionRow();
        }

        $this->recalculateCapital();
    }

    public function addOther()
    {
        $this->other_costs[] = $this->defaultOtherCostRow();
    }

    public function updatedOtherCosts()
    {
        $this->recalculateCapital();
        $this->recalculatePcsCapital();
    }

    public function removeOther($index)
    {
        unset($this->other_costs[$index]);
        $this->other_costs = array_values($this->other_costs);

        if (empty($this->other_costs)) {
            $this->other_costs[] = $this->defaultOtherCostRow();
        }

        $this->recalculateCapital();
    }

    public function setMaterial($index, $materialId)
    {
        if (! array_key_exists($index, $this->product_compositions)) {
            return;
        }

        $this->product_compositions[$index]['material_id'] = $materialId;
        $this->product_compositions[$index]['unit_id'] = '';
        $this->product_compositions[$index]['material_price'] = 0;
        $this->recalculateCapital();
    }

    public function setSoloMaterial($index, $materialId)
    {
        if ($materialId) {
            $this->product_compositions[$index]['material_id'] = $materialId;
            $materialDetail = MaterialDetail::where('material_id', $materialId)
                ->where('is_main', true)
                ->first();
            $this->setUnit($index, $materialDetail ? $materialDetail->unit_id : null);
            $this->product_compositions[$index]['material_quantity'] = 1;
            $this->recalculateCapital();
        }
    }

    public function setUnit($index, $unitId)
    {
        if (! array_key_exists($index, $this->product_compositions)) {
            return;
        }

        if ($unitId) {
            $this->product_compositions[$index]['unit_id'] = $unitId;
            $materialDetail = MaterialDetail::where('material_id', $this->product_compositions[$index]['material_id'] ?? null)
                ->where('unit_id', $unitId)
                ->first();

            $this->product_compositions[$index]['material_price'] = $materialDetail?->supply_price ?? 0;
        } else {
            $this->product_compositions[$index]['unit_id'] = '';
            $this->product_compositions[$index]['material_price'] = 0;
        }

        $this->recalculateCapital();
    }

    public function updatedProductImage()
    {
        $this->validate([
            'product_image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $this->previewImage = $this->product_image->temporaryUrl();
    }

    public function removeImage()
    {
        $this->reset('product_image', 'previewImage');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'product_image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        DB::transaction(function () {
            $product = Product::findOrFail($this->product_id);
            $product->update($this->productPayload());

            if ($this->product_image) {
                if ($product->product_image) {
                    $oldImagePath = public_path('storage/'.$product->product_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $product->product_image = $this->product_image->store('product_images', 'public');
                $product->save();
            }

            $this->syncCategories($product);
            $this->syncCompositions($product);
            $this->syncOtherCosts($product);
        });

        return redirect()->intended(route('produk'))->with('success', 'Produk berhasil diperbarui.');
    }

    protected function recalculateCapital()
    {
        $compositionTotal = collect($this->product_compositions)
            ->sum(fn ($c) => ($c['material_price'] ?? 0) * ($c['material_quantity'] ?? 0));

        $otherTotal = collect($this->other_costs)
            ->sum('price');

        $this->capital = $otherTotal + $compositionTotal;
    }

    public function updatedPrice($value)
    {
        $this->resetErrorBag('price');

        if (! is_numeric($value)) {
            return;
        }

        $numericValue = (float) $value;

        if ($this->pcs > 1 && $numericValue < $this->pcs_capital) {
            $this->addError('price', 'Harga jual per unit tidak boleh kurang dari modal per unit.');
        }

        if ($numericValue < $this->capital) {
            $this->addError('price', 'Harga jual tidak boleh kurang dari total modal.');
        }
    }

    protected function recalculatePcsCapital()
    {
        if ($this->pcs < 1) {
            $this->pcs = 1;
        }

        $this->pcs_capital = $this->pcs ? $this->capital / $this->pcs : $this->capital;
    }

    public function updatedPcs($value)
    {
        $this->resetErrorBag('pcs');

        if ($value < 1) {
            $this->addError('pcs', 'Jumlah pcs tidak boleh kurang dari 1.');
            $this->pcs = 1;
        }

        $this->recalculateCapital();
        $this->recalculatePcsCapital();
    }

    public function updatedProductCompositions()
    {
        $this->product_compositions = array_map(function ($composition) {
            $normalized = array_merge($this->defaultCompositionRow(), array_filter($composition, fn ($value) => $value !== null));

            return [
                'material_id' => $normalized['material_id'] ?? '',
                'material_quantity' => (float) ($normalized['material_quantity'] ?? 0),
                'unit_id' => $normalized['unit_id'] ?? '',
                'material_price' => (float) ($normalized['material_price'] ?? 0),
            ];
        }, $this->product_compositions);
        $this->recalculateCapital();
        $this->recalculatePcsCapital();
    }

    public function confirmDelete()
    {
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus produk ini?', [
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
        $product = Product::find($this->product_id);

        if ($product) {
            $product->delete();
            // Hapus gambar produk jika ada
            if ($product->product_image) {
                $oldImagePath = public_path('storage/'.$product->product_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            return redirect()->intended(route('produk'))->with('success', 'Produk berhasil dihapus.');
        }
    }

    protected function resetRecipeState(bool $isRecipe): void
    {
        $this->is_recipe = $isRecipe;
        $this->is_other = false;
        $this->product_compositions = [$this->defaultCompositionRow()];
        $this->other_costs = [$this->defaultOtherCostRow()];
        $this->pcs = 1;
        $this->recalculateCapital();
    }

    protected function defaultCompositionRow(): array
    {
        return [
            'material_id' => '',
            'material_quantity' => 0,
            'unit_id' => '',
            'material_price' => 0,
        ];
    }

    protected function defaultOtherCostRow(): array
    {
        return [
            'type_cost_id' => '',
            'name' => '',
            'price' => 0,
        ];
    }

    protected function productPayload(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'method' => $this->selectedMethods,
            'is_recipe' => $this->is_recipe,
            'is_active' => $this->is_active,
            'is_recommended' => $this->is_recommended,
            'is_other' => $this->is_other,
            'pcs' => $this->pcs,
            'capital' => $this->capital,
            'pcs_capital' => $this->pcs_capital,
            'suhu_ruangan' => $this->suhu_ruangan,
            'suhu_dingin' => $this->suhu_dingin,
            'suhu_beku' => $this->suhu_beku,
        ];
    }

    protected function syncCategories(Product $product): void
    {
        if (empty($this->category_ids)) {
            return;
        }

        $product->product_categories()->delete();

        foreach ($this->category_ids as $categoryId) {
            ProductCategory::create([
                'product_id' => $product->id,
                'category_id' => $categoryId,
            ]);
        }
    }

    protected function syncCompositions(Product $product): void
    {
        $product->product_compositions()->delete();

        if (empty($this->product_compositions)) {
            return;
        }

        foreach ($this->product_compositions as $composition) {
            if (blank($composition['material_id'])) {
                continue;
            }

            $product->product_compositions()->create([
                'material_id' => $composition['material_id'],
                'material_quantity' => $composition['material_quantity'],
                'unit_id' => $composition['unit_id'] ?: null,
            ]);
        }
    }

    protected function syncOtherCosts(Product $product): void
    {
        $product->other_costs()->delete();

        if (empty($this->other_costs)) {
            return;
        }

        foreach ($this->other_costs as $cost) {
            if (blank($cost['type_cost_id']) && blank($cost['name']) && empty($cost['price'])) {
                continue;
            }

            $product->other_costs()->create([
                'type_cost_id' => $cost['type_cost_id'] ?: null,
                'name' => $cost['name'],
                'price' => (float) ($cost['price'] ?? 0),
            ]);
        }
    }

    protected function categoryOptions()
    {
        return once(fn () => \App\Models\Category::orderBy('name')->get(['id', 'name']));
    }

    protected function recipeMaterials()
    {
        return once(fn () => Material::where('is_recipe', false)
            ->with(['material_details.unit'])
            ->orderBy('name')
            ->get());
    }

    protected function readyMaterials()
    {
        return once(fn () => Material::where('is_recipe', true)
            ->with(['material_details.unit', 'batches.unit'])
            ->orderBy('name')
            ->get());
    }

    protected function typeCostOptions()
    {
        return once(fn () => \App\Models\TypeCost::orderBy('name')->get(['id', 'name']));
    }

    protected function resolveSoloInventory(): ?array
    {
        $materialId = $this->product_compositions[0]['material_id'] ?? null;

        if (! $materialId) {
            return null;
        }

        $material = $this->readyMaterials()->firstWhere('id', $materialId);

        if (! $material) {
            return null;
        }

        $mainDetail = $material->material_details->firstWhere('is_main', true);
        $mainUnitAlias = $mainDetail?->unit->alias;

        $batches = $material->batches->map(function ($batch) use ($material) {
            $conversion = $material->material_details->firstWhere('unit_id', $batch->unit_id);
            $mainQuantity = $conversion ? $batch->batch_quantity * ($conversion->quantity ?? 1) : $batch->batch_quantity;

            return [
                'number' => $batch->batch_number,
                'quantity' => $batch->batch_quantity,
                'unit_alias' => $batch->unit->alias ?? '',
                'date' => $batch->date,
                'main_quantity' => $mainQuantity,
            ];
        })->values();

        return [
            'material' => $material,
            'batches' => $batches,
            'main_unit_alias' => $mainUnitAlias,
            'total_main' => $batches->sum('main_quantity'),
        ];
    }

    public function render()
    {
        return view('livewire.product.rincian', [
            'categoryOptions' => $this->categoryOptions(),
            'recipeMaterials' => $this->recipeMaterials(),
            'readyMaterials' => $this->readyMaterials(),
            'soloInventory' => $this->resolveSoloInventory(),
            'typeCosts' => $this->typeCostOptions(),
        ]);
    }
}

<?php

namespace App\Livewire\Product;

use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialDetail;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;

class Form extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, WithFileUploads;

    // Product ID (null for create, has value for edit)
    public $product_id;

    // Form fields
    public $name = '';

    public $description = null;

    public $category_ids = [];

    public $selectedMethods = ['pesanan-reguler'];

    public $is_recipe = false;

    public $is_active = false;

    public $is_recommended = false;

    public $is_other = false;

    public $pcs = 1;

    public $price = 0;

    public $stock = 0;

    public $suhu_ruangan = 0;

    public $suhu_dingin = 0;

    public $suhu_beku = 0;

    // Image
    public $product_image;

    public $previewImage = null;

    // Compositions & Costs
    public $product_compositions = [];

    public $other_costs = [];

    // Calculations
    public $capital = 0;

    public $pcs_capital = 0;

    // UI State
    public $showHistoryModal = false;

    public $activityLogs = [];

    public $product;

    protected $listeners = [
        'delete',
    ];

    protected $messages = [
        'name.required' => 'Nama produk tidak boleh kosong.',
        'product_image.image' => 'File yang diunggah harus berupa gambar.',
        'product_image.max' => 'Ukuran gambar tidak boleh lebih dari 2 MB.',
        'product_image.mimes' => 'Format gambar yang diizinkan adalah jpg, jpeg, png.',
        'selectedMethods.min' => 'Pilih minimal satu metode penjualan.',
    ];

    public function mount($id = null): void
    {
        $this->product_id = $id;

        if ($id) {
            // Edit mode
            View::share('title', 'Rincian Produk');
            $this->loadProduct($id);
        } else {
            // Create mode
            View::share('title', 'Tambah Produk');
            $this->product_compositions = [$this->defaultCompositionRow()];
            $this->other_costs = [$this->defaultOtherCostRow()];
        }

        View::share('mainTitle', 'Inventori');
    }

    protected function loadProduct($id): void
    {
        $product = Product::with(['product_compositions', 'product_categories', 'other_costs'])
            ->findOrFail($id);

        $this->product = $product;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->category_ids = $product->product_categories->pluck('category_id')->toArray();
        $this->selectedMethods = $product->method ?? [];
        $this->is_recipe = $product->is_recipe ?? false;
        $this->is_active = $product->is_active ?? false;
        $this->is_recommended = $product->is_recommended ?? false;
        $this->is_other = $product->is_other ?? false;
        $this->pcs = $product->pcs ?? 1;
        $this->price = $product->price ?? 0;
        $this->stock = $product->stock ?? 0;
        $this->capital = $product->capital ?? 0;
        $this->pcs_capital = $product->pcs_capital ?? 0;
        $this->suhu_ruangan = $product->suhu_ruangan ?? 0;
        $this->suhu_dingin = $product->suhu_dingin ?? 0;
        $this->suhu_beku = $product->suhu_beku ?? 0;

        // Load compositions
        $this->product_compositions = $product->product_compositions->map(function ($composition) {
            $materialDetail = MaterialDetail::where('material_id', $composition->material_id)
                ->where('unit_id', $composition->unit_id)
                ->first();

            $price = $materialDetail?->supply_price ?? 0;

            // Jika harga 0, coba konversi dari unit lain yang sudah ada harga
            if ($price == 0) {
                $targetUnit = \App\Models\Unit::find($composition->unit_id);
                $otherDetails = MaterialDetail::where('material_id', $composition->material_id)
                    ->where('unit_id', '!=', $composition->unit_id)
                    ->where('supply_price', '>', 0)
                    ->with('unit')
                    ->get();

                foreach ($otherDetails as $otherDetail) {
                    if ($otherDetail->unit && $targetUnit) {
                        // Konversi harga dari unit lain ke unit target
                        // Misal: 1kg = 1000gram, harga Rp10.000/kg → Rp10.000/1000 = Rp10/gram
                        $convertedQuantity = $otherDetail->unit->convertTo(1, $targetUnit);
                        if ($convertedQuantity !== null && $convertedQuantity != 0) {
                            $price = $otherDetail->supply_price / $convertedQuantity;
                            break;
                        }
                    }
                }
            }

            return [
                'material_id' => $composition->material_id,
                'material_quantity' => $composition->material_quantity,
                'unit_id' => $composition->unit_id,
                'material_price' => $price,
            ];
        })->toArray();

        if (empty($this->product_compositions)) {
            $this->product_compositions = [$this->defaultCompositionRow()];
        }

        // Load other costs
        $this->other_costs = $product->other_costs->map(function ($cost) {
            return [
                'type_cost_id' => $cost->type_cost_id,
                'name' => $cost->name,
                'price' => $cost->price,
            ];
        })->toArray();

        if (empty($this->other_costs)) {
            $this->other_costs = [$this->defaultOtherCostRow()];
        }

        // Set preview image
        $this->previewImage = $product->product_image ? env('APP_URL') . '/storage/' . $product->product_image : null;
    }

    public function riwayatPembaruan(): void
    {
        $this->activityLogs = Activity::inLog('products')
            ->where('subject_id', $this->product_id)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function updatedProductImage(): void
    {
        $this->validate([
            'product_image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);

        $this->previewImage = $this->product_image->temporaryUrl();
    }

    public function removeImage(): void
    {
        $this->reset('product_image', 'previewImage');
    }

    public function updatedIsRecipe($value): void
    {
        $this->resetRecipeState((bool) $value);
    }

    public function addComposition(): void
    {
        $this->product_compositions[] = $this->defaultCompositionRow();
    }

    public function removeComposition($index): void
    {
        if (count($this->product_compositions) > 1) {
            unset($this->product_compositions[$index]);
            $this->product_compositions = array_values($this->product_compositions);
        }

        $this->recalculateCapital();
    }

    public function addOther(): void
    {
        $this->other_costs[] = $this->defaultOtherCostRow();
    }

    public function removeOther($index): void
    {
        if (count($this->other_costs) > 1) {
            unset($this->other_costs[$index]);
            $this->other_costs = array_values($this->other_costs);
        }

        $this->recalculateCapital();
    }

    public function setMaterial($index, $materialId): void
    {
        if (! array_key_exists($index, $this->product_compositions)) {
            return;
        }

        $this->product_compositions[$index]['material_id'] = $materialId;
        $this->product_compositions[$index]['unit_id'] = '';
        $this->product_compositions[$index]['material_price'] = 0;
        $this->recalculateCapital();
    }

    public function setSoloMaterial($index, $materialId): void
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

    public function setUnit($index, $unitId): void
    {
        if (! array_key_exists($index, $this->product_compositions)) {
            return;
        }

        if ($unitId) {
            $this->product_compositions[$index]['unit_id'] = $unitId;
            $materialId = $this->product_compositions[$index]['material_id'] ?? null;
            $materialDetail = MaterialDetail::where('material_id', $materialId)
                ->where('unit_id', $unitId)
                ->first();

            $price = $materialDetail?->supply_price ?? 0;

            // Jika harga 0, coba konversi dari unit lain yang sudah ada harga
            if ($price == 0 && $materialId) {
                $targetUnit = \App\Models\Unit::find($unitId);
                $otherDetails = MaterialDetail::where('material_id', $materialId)
                    ->where('unit_id', '!=', $unitId)
                    ->where('supply_price', '>', 0)
                    ->with('unit')
                    ->get();

                foreach ($otherDetails as $otherDetail) {
                    if ($otherDetail->unit && $targetUnit) {
                        // Konversi harga dari unit lain ke unit target
                        // Misal: 1kg = 1000gram, harga Rp10.000/kg → Rp10.000/1000 = Rp10/gram
                        $convertedQuantity = $otherDetail->unit->convertTo(1, $targetUnit);
                        if ($convertedQuantity !== null && $convertedQuantity != 0) {
                            $price = $otherDetail->supply_price / $convertedQuantity;
                            break;
                        }
                    }
                }
            }

            $this->product_compositions[$index]['material_price'] = $price;
        } else {
            $this->product_compositions[$index]['unit_id'] = '';
            $this->product_compositions[$index]['material_price'] = 0;
        }

        $this->recalculateCapital();
    }

    public function updatedProductCompositions(): void
    {
        $this->product_compositions = array_map(function ($composition) {
            return [
                'material_id' => $composition['material_id'] ?? '',
                'material_quantity' => (float) ($composition['material_quantity'] ?? 0),
                'unit_id' => $composition['unit_id'] ?? '',
                'material_price' => (float) ($composition['material_price'] ?? 0),
            ];
        }, $this->product_compositions);

        $this->recalculateCapital();
    }

    public function updatedOtherCosts(): void
    {
        $this->recalculateCapital();
    }

    public function updatedPcs($value): void
    {
        $this->resetErrorBag('pcs');

        if ($value < 1) {
            $this->addError('pcs', 'Jumlah pcs tidak boleh kurang dari 1.');
            $this->pcs = 1;
        }

        $this->recalculateCapital();
    }

    public function updatedPrice($value): void
    {
        $this->resetErrorBag('price');

        if (! is_numeric($value)) {
            return;
        }

        $numericValue = (float) $value;

        if ($this->pcs > 1 && $numericValue < $this->pcs_capital) {
            $this->addError('price', 'Harga jual per unit tidak boleh kurang dari modal per unit.');
        }

        if ($numericValue < $this->capital && $this->pcs == 1) {
            $this->addError('price', 'Harga jual tidak boleh kurang dari total modal.');
        }
    }

    protected function recalculateCapital(): void
    {
        $compositionTotal = collect($this->product_compositions)
            ->sum(fn($c) => ($c['material_price'] ?? 0) * ($c['material_quantity'] ?? 0));

        $otherTotal = collect($this->other_costs)
            ->sum('price');

        $this->capital = $otherTotal + $compositionTotal;
        $this->recalculatePcsCapital();
    }

    protected function recalculatePcsCapital(): void
    {
        if ($this->pcs < 1) {
            $this->pcs = 1;
        }

        $this->pcs_capital = $this->pcs ? $this->capital / $this->pcs : $this->capital;
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

    public function confirmDelete(): void
    {
        $this->alert('warning', 'Hapus Produk?', [
            'text' => 'Apakah Anda yakin ingin menghapus produk ini? Data yang dihapus tidak dapat dikembalikan.',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'confirmButtonColor' => '#ef4444',
            'cancelButtonColor' => '#6b7280',
            'width' => '400',
            'padding' => '1.5rem',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    public function delete()
    {
        $product = Product::find($this->product_id);

        if (! $product) {
            return redirect()->intended(route('produk'))->with('error', 'Produk tidak ditemukan.');
        }

        DB::transaction(function () use ($product) {
            // Delete image if exists
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }

            $product->delete();
        });

        return redirect()->intended(route('produk'))->with('success', 'Produk berhasil dihapus.');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:500',
            'product_image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'selectedMethods' => 'array|min:1',
            'category_ids' => 'array',
            'is_recipe' => 'boolean',
            'is_active' => 'boolean',
            'is_recommended' => 'boolean',
            'is_other' => 'boolean',
            'pcs' => 'integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () {
            if ($this->product_id) {
                $this->updateProduct();
            } else {
                $this->createProduct();
            }
        });

        return redirect()->intended(route('produk'))
            ->with('success', 'Produk berhasil ' . ($this->product_id ? 'diperbarui' : 'ditambahkan') . '.');
    }

    protected function createProduct(): void
    {
        $product = Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => 0,
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
            'product_image' => $this->product_image ? $this->product_image->store('product_images', 'public') : null,
        ]);

        $this->syncCategories($product);
        $this->syncCompositions($product);
        $this->syncOtherCosts($product);
    }

    protected function updateProduct(): void
    {
        $product = Product::findOrFail($this->product_id);

        // Handle image upload
        $imageData = [];
        if ($this->product_image) {
            // Delete old image
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                Storage::disk('public')->delete($product->product_image);
            }
            $imageData['product_image'] = $this->product_image->store('product_images', 'public');
        }

        // Update product
        $product->update(array_merge([
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
        ], $imageData));

        // Update relationships
        $product->product_categories()->delete();
        $product->product_compositions()->delete();
        $product->other_costs()->delete();

        $this->syncCategories($product);
        $this->syncCompositions($product);
        $this->syncOtherCosts($product);
    }

    protected function syncCategories(Product $product): void
    {
        if (empty($this->category_ids)) {
            return;
        }

        foreach ($this->category_ids as $categoryId) {
            ProductCategory::create([
                'product_id' => $product->id,
                'category_id' => $categoryId,
            ]);
        }
    }

    protected function syncCompositions(Product $product): void
    {
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

    protected function categoryOptions(): Collection
    {
        return once(fn() => Category::orderBy('name')->get(['id', 'name']));
    }

    protected function recipeMaterials(): Collection
    {
        return once(fn() => Material::where('is_recipe', false)
            ->with(['material_details.unit'])
            ->orderBy('name')
            ->get());
    }

    protected function readyMaterials(): Collection
    {
        return once(fn() => Material::where('is_recipe', true)
            ->with(['material_details.unit', 'batches.unit'])
            ->orderBy('name')
            ->get());
    }

    protected function typeCostOptions(): Collection
    {
        return once(fn() => \App\Models\TypeCost::orderBy('name')->get(['id', 'name']));
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
        return view('livewire.product.form', [
            'categoryOptions' => $this->categoryOptions(),
            'recipeMaterials' => $this->recipeMaterials(),
            'readyMaterials' => $this->readyMaterials(),
            'soloInventory' => $this->resolveSoloInventory(),
            'typeCosts' => $this->typeCostOptions(),
        ]);
    }
}

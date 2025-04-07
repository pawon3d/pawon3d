<?php

namespace App\Livewire\Product;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use App\Models\Material;
use App\Models\ProcessedMaterial;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use WithPagination, WithFileUploads, LivewireAlert;

    public $name, $category_id, $price, $stock = 0, $product_image, $is_ready = false, $delete_id;
    public $product_compositions = [];
    public $search = '';
    public $showAddModal = false;
    public $showEditModal = false;
    public $showDetailModal = false;
    public $editId = null;
    public $detailData = null;
    public $activeTab = 'material';
    public $previewImage = null;

    protected $listeners = [
        'delete'
    ];

    protected $rules = [
        'name' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|integer',
        'stock' => 'required|integer',
        'product_image' => 'nullable|image|max:2048',
        'is_ready' => 'boolean',
        'product_compositions' => 'required|array|min:1',
        'product_compositions.*.material_id' => [
            'nullable',
            'required_without:product_compositions.*.processed_material_id',
            'exists:materials,id'
        ],
        'product_compositions.*.processed_material_id' => [
            'nullable',
            'required_without:product_compositions.*.material_id',
            'exists:processed_materials,id'
        ],
    ];

    public function mount()
    {
        View::share('title', 'Produk');

        $this->product_compositions = [[
            'material_id' => '',
            'processed_material_id' => '',
            'material_quantity' => 0,
            'processed_material_quantity' => 0,
            'material_unit' => ''
        ]];
    }

    public function render()
    {
        return view('livewire.product.index', [
            'products' => Product::with(['category', 'product_compositions'])
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->paginate(10),
            'categories' => Category::all(),
            'materials' => Material::all(),
            'processedMaterials' => ProcessedMaterial::all()
        ]);
    }

    public function addComposition()
    {
        $this->product_compositions[] = [
            'material_id' => '',
            'processed_material_id' => '',
            'material_quantity' => 0,
            'processed_material_quantity' => 0,
            'material_unit' => ''
        ];
    }

    public function removeComposition($index)
    {
        unset($this->product_compositions[$index]);
        $this->product_compositions = array_values($this->product_compositions);
    }

    public function setMaterial($index, $materialId)
    {
        $this->product_compositions[$index]['material_id'] = $materialId;
        $this->product_compositions[$index]['processed_material_id'] = null; // Reset yang lain

        if ($materialId) {
            $material = Material::find($materialId);
            $this->product_compositions[$index]['material_unit'] = $material->unit;
        }
    }

    public function setProcessedMaterial($index, $processedMaterialId)
    {
        $this->product_compositions[$index]['processed_material_id'] = $processedMaterialId;
        $this->product_compositions[$index]['material_id'] = null; // Reset yang lain

        if ($processedMaterialId) {
            $pm = ProcessedMaterial::find($processedMaterialId);
            $this->product_compositions[$index]['processed_material_quantity'] = $pm->quantity;
        }
    }

    public function store()
    {
        $this->validate();

        $product = Product::create([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'stock' => $this->stock,
            'is_ready' => $this->is_ready,
        ]);

        if ($this->product_image) {
            $product->product_image = $this->product_image->store('product_images', 'public');
            $product->save();
        }

        foreach ($this->product_compositions as $composition) {
            // Convert empty string to null
            $cleanData = [
                'material_id' => !empty($composition['material_id']) ? $composition['material_id'] : null,
                'processed_material_id' => !empty($composition['processed_material_id']) ? $composition['processed_material_id'] : null,
                'material_quantity' => $composition['material_quantity'],
                'processed_material_quantity' => $composition['processed_material_quantity'],
                'material_unit' => $composition['material_unit'] ?? null,
            ];

            $product->product_compositions()->create($cleanData);
        }

        $this->resetForm();
        $this->showAddModal = false;
        $this->alert('success', 'Produk berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $product = Product::with('product_compositions')->find($id);
        $this->editId = $id;
        $this->name = $product->name;
        $this->category_id = $product->category_id;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->is_ready = $product->is_ready;
        $this->product_compositions = $product->product_compositions->toArray();
        $this->previewImage = $product->product_image;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();

        $product = Product::find($this->editId);
        $product->update([
            'name' => $this->name,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'stock' => $this->stock,
            'is_ready' => $this->is_ready,
        ]);

        if ($this->product_image) {
            Storage::disk('public')->delete($product->product_image);
            $product->product_image = $this->product_image->store('product_images', 'public');
            $product->save();
        }

        $product->product_compositions()->delete();
        foreach ($this->product_compositions as $composition) {
            // Convert empty string to null
            $cleanData = [
                'material_id' => !empty($composition['material_id']) ? $composition['material_id'] : null,
                'processed_material_id' => !empty($composition['processed_material_id']) ? $composition['processed_material_id'] : null,
                'material_quantity' => $composition['material_quantity'],
                'processed_material_quantity' => $composition['processed_material_quantity'],
                'material_unit' => $composition['material_unit'] ?? null,
            ];

            $product->product_compositions()->create($cleanData);
        }

        $this->resetForm();
        $this->showEditModal = false;
        $this->alert('success', 'Produk berhasil diperbarui!');
    }

    public function confirmDelete($id)
    {

        // Simpan ID ke dalam properti
        $this->delete_id = $id;

        // Konfirmasi menggunakan Livewire Alert
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
        $product = Product::find($this->delete_id);
        if ($product->product_image) {
            Storage::disk('public')->delete($product->product_image);
        }
        $product->delete();
        $this->alert('success', 'Produk berhasil dihapus!');
        $this->reset('delete_id');
    }

    public function showDetail($id)
    {
        $this->detailData = Product::with(['product_compositions.material', 'product_compositions.processed_material'])
            ->find($id);
        $this->showDetailModal = true;
    }

    private function resetForm()
    {
        $this->reset([
            'name',
            'category_id',
            'price',
            'stock',
            'product_image',
            'is_ready',
            'editId',
            'previewImage'
        ]);
        $this->product_compositions = [[
            'material_id' => '',
            'processed_material_id' => '',
            'material_quantity' => 0,
            'processed_material_quantity' => 0,
            'material_unit' => ''
        ]];
        $this->resetErrorBag();
    }

    public function updatedProductImage()
    {
        $this->previewImage = $this->product_image->temporaryUrl();
    }
}
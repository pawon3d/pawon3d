<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;
use App\Models\Material;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\View;

class Tambah extends Component
{
    use WithFileUploads;

    public $product_image, $name, $description, $category_ids, $is_recipe = false, $is_active = false, $is_recommended = false, $is_other = false, $is_many = false, $pcs = 1, $capital = 0;
    public $previewImage;
    public $product_compositions = [];
    public $other_costs = [];

    public $price = 0;
    public $stock = 0;
    public $method;


    protected $listeners = [
        'updatedProductImage' => 'updatedProductImage',
        'removeImage' => 'removeImage',
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

    public function mount($method)
    {
        View::share('title', 'Tambah Produk');

        $this->method = $method;

        $this->product_compositions = [[
            'material_id' => '',
            'material_quantity' => 0,
            'material_unit' => ''
        ]];
        $this->other_costs = [[
            'name' => '',
            'price' => 0,
        ]];

        $this->recalculateCapital();
    }

    public function addComposition()
    {
        $this->product_compositions[] = [
            'material_id' => '',
            'processed_material_name' => '',
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

    public function addOther()
    {
        $this->other_costs[] = [
            'name' => '',
            'price' => 0,
        ];
    }

    public function updatedOtherCosts()
    {
        $this->recalculateCapital();
    }

    public function removeOther($index)
    {
        unset($this->other_costs[$index]);
        $this->other_costs = array_values($this->other_costs);
    }

    public function setMaterial($index, $materialId)
    {
        $this->product_compositions[$index]['material_id'] = $materialId;

        if ($materialId) {
            $material = Material::find($materialId);
            $this->product_compositions[$index]['material_unit'] = $material->unit;
        }
    }

    public function updatedProductImage()
    {
        $this->validate([
            'product_image' => 'image|max:2048|mimes:jpg,jpeg,png',
        ]);

        // Untuk preview langsung setelah upload
        $this->previewImage = $this->product_image->temporaryUrl();
    }

    public function removeImage()
    {
        $this->reset('product_image', 'previewImage');
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'product_image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ]);
        $product = Product::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => 0,
            'method' => $this->method,
            'is_recipe' => $this->is_recipe,
            'is_active' => $this->is_active,
            'is_recommended' => $this->is_recommended,
            'is_other' => $this->is_other,
            'is_many' => $this->is_many,
            'pcs' => $this->pcs,
            'capital' => $this->capital,
        ]);

        if ($this->product_image) {
            $product->product_image = $this->product_image->store('product_images', 'public');
            $product->save();
        }

        if ($this->category_ids) {
            foreach ($this->category_ids as $category_id) {
                ProductCategory::create([
                    'product_id' => $product->id,
                    'category_id' => $category_id,
                ]);
            }
        }

        if ($this->product_compositions) {
            foreach ($this->product_compositions as $composition) {
                $cleanData = [
                    'material_id' => !empty($composition['material_id']) ? $composition['material_id'] : null,
                    'material_quantity' => $composition['material_quantity'],
                    'material_unit' => $composition['material_unit'] ?? null,
                ];

                $product->product_compositions()->create($cleanData);
            }
        }
        if ($this->other_costs) {
            foreach ($this->other_costs as $cost) {
                $cleanData = [
                    'name' => $cost['name'],
                    'price' => $cost['price'],
                ];

                $product->other_costs()->create($cleanData);
            }
        }


        $this->resetForm();
        return redirect()->intended(route('produk'))->with('success', 'Produk berhasil ditambahkan.');
    }

    protected function recalculateCapital()
    {
        // $compositionTotal = collect($this->product_compositions)
        //     ->sum(fn($c) => ($c['material_price'] ?? 0) * ($c['material_quantity'] ?? 0));

        $otherTotal = collect($this->other_costs)
            ->sum('price');

        $this->capital = $otherTotal;
    }

    public function updatedPrice($value)
    {
        // reset dulu agar pesan lama hilang
        $this->resetErrorBag('price');

        // validasi dasar numeric dan required
        $this->validateOnly('price');

        // custom: price tidak boleh kurang dari modal
        if ($value < $this->capital) {
            $this->addError('price', "Harga jual tidak boleh kurang dari modal.");
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'product_image',
            'description',
            'category_ids',
            'previewImage',
        ]);
    }

    public function render()
    {
        return view('livewire.product.tambah', [
            'categories' => \App\Models\Category::lazy(),
            'materials' => \App\Models\Material::lazy(),
        ]);
    }
}
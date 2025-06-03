<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Salin extends Component
{
    use WithPagination, WithFileUploads, LivewireAlert;

    public $activityLogs = [];
    public $filterStatus = '';
    public $search = '';
    public $viewMode = 'grid';
    public $fromMethod = '';
    public $toMethod = 'pesanan-reguler';
    public $title = '';
    public array $selectedProducts = [];

    protected $queryString = ['viewMode', 'fromMethod'];

    public function mount($method)
    {
        $this->toMethod = $method;
        if ($method == 'pesanan-reguler') {
            $this->fromMethod = 'pesanan-kotak';
            $this->title = 'Pesanan Reguler';
        } else {
            $this->fromMethod = 'pesanan-reguler';
            if ($method == 'pesanan-kotak') {
                $this->title = 'Pesanan Kotak';
            } else {
                $this->title = 'Siap Beli';
            }
        };

        View::share('title', 'Salin Produk ke' . $this->title);

        $this->viewMode = session('viewMode', 'grid');
    }

    public function updatedViewMode($value)
    {
        session()->put('viewMode', $value);
    }

    public function updatedFromMethod($value)
    {
        session()->put('fromMethod', $value);
    }

    public function saveCopy()
    {

        if (empty($this->selectedProducts)) {
            $this->alert('error', 'Tidak ada produk yang dipilih untuk disalin.');
            return;
        }

        $products = Product::with(['product_compositions', 'product_categories', 'other_costs'])
            ->where('method', $this->fromMethod)
            ->whereIn('id', $this->selectedProducts)
            ->get();

        $msgMethod = $this->fromMethod == 'pesanan-reguler' ? 'Pesanan Reguler' : ($this->fromMethod == 'pesanan-kotak' ? 'Pesanan Kotak' : 'Siap Beli');

        foreach ($products as $product) {
            $newProduct = $product->replicate();
            $newProduct->id = Str::uuid();
            $newProduct->name = $product->name . ' (Salinan dari ' . $msgMethod . ')';
            $newProduct->method = $this->toMethod;

            // Salin gambar jika ada
            if ($product->product_image && Storage::disk('public')->exists($product->product_image)) {
                $extension = pathinfo($product->product_image, PATHINFO_EXTENSION);
                $newImageName = 'product_images/' . Str::uuid() . '.' . $extension;

                Storage::disk('public')->copy($product->product_image, $newImageName);
                $newProduct->product_image = $newImageName;
            }

            $newProduct->push();

            // Copy Komposisi
            foreach ($product->product_compositions as $composition) {
                $newProduct->product_compositions()->create([
                    'material_id' => $composition->material_id,
                    'unit_id' => $composition->unit_id,
                    'material_quantity' => $composition->material_quantity,
                ]);
            }

            foreach ($product->product_categories as $category) {
                $newProduct->product_categories()->create([
                    'category_id' => $category->category_id,
                ]);
            }

            // Copy Biaya Lain
            foreach ($product->other_costs as $cost) {
                $newProduct->other_costs()->create([
                    'name' => $cost->name,
                    'price' => $cost->price,
                ]);
            }
        }

        $this->selectedProducts = [];
        return redirect()->route('produk')->with('success', 'Produk berhasil disalin dari ' . $msgMethod . ' ke ' . $this->title . '!');
    }
    public function render()
    {
        return view('livewire.product.salin', [
            'products' => Product::with(['product_categories', 'product_compositions', 'reviews'])
                ->where('method', $this->fromMethod)
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->paginate(10),
        ]);
    }
}

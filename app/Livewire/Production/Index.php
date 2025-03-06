<?php

namespace App\Livewire\Production;

use App\Models\Product;
use Livewire\Component;
use App\Models\Production;
use App\Models\Transaction;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert, WithPagination;

    public $openAdd = false;
    public $openEdit = false;
    public $productionId;

    // Tentukan tab aktif: 'pesanan' atau 'siap_beli'
    public $activeTab = 'pesanan';
    public $typeFilter = 'all';
    // Properti form produksi
    public $transaction_id = '';
    public $transaction_detail_id = '';


    public $product_id = '';
    public $count = 1;
    public $status = 'menunggu dibuat';
    public $search = '';
    public $time = 0;
    public $quantity = 0;
    public $delete_id;
    public $compositionList = [];

    protected $listeners = [
        'delete'
    ];

    protected function rules()
    {
        // Validasi berbeda tergantung tab aktif
        if ($this->activeTab === 'pesanan') {
            return [
                'transaction_id'        => 'required',
                'transaction_detail_id' => 'required',
                'count'                 => 'required|integer',
                'status'                => 'required|string',
                'time'                  => 'required|string',
            ];
        } else {
            return [
                'product_id' => 'required',
                'quantity'   => 'required|integer|min:1',
                'count'      => 'required|integer',
                'status'     => 'required|string',
                'time'       => 'required|string',
            ];
        }
    }


    public function mount()
    {
        View::share('title', 'Produksi');
    }

    public function render()
    {
        // Mengambil daftar produksi dengan relasi produk
        $productions = Production::with('product', 'product.product_compositions')
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->latest()
            ->paginate(10);
        // Mengambil transaksi dengan tipe "pesanan" (untuk dipakai pada select)
        $transactions = Transaction::where('type', 'pesanan')
            ->with('details.product')
            ->get();

        $readyProducts = Product::where('is_ready', true)->get();


        return view('livewire.production.index', compact('productions', 'transactions', 'readyProducts'));
    }

    public function updatedTransactionDetailId()
    {
        $transaction = Transaction::find($this->transaction_id);
        if ($transaction) {
            $detail = $transaction->details->firstWhere('id', $this->transaction_detail_id);
            if ($detail) {
                // Ambil quantity dari detail transaksi
                $this->quantity = $detail->quantity;
                // Ambil product_id dari detail, agar produksi tersimpan untuk produk itu
                $this->product_id = $detail->product->id;
                // Ambil data komposisi dari produk
                $this->compositionList = $detail->product->product_compositions ?? [];
            } else {
                $this->quantity = 0;
                $this->compositionList = [];
            }
        }
    }

    public function updatedProductId()
    {
        if ($this->activeTab === 'siap_beli') {
            $product = Product::where('is_ready', true)->find($this->product_id);
            if ($product) {
                $this->compositionList = $product->product_compositions ?? [];
            } else {
                $this->compositionList = [];
            }
        }
    }

    public function addProduction()
    {
        $data = $this->validate();

        if ($this->activeTab === 'pesanan') {
            $data['product_id'] = $this->product_id; // diambil dari detail
            $data['quantity']   = $this->quantity;   // otomatis dari detail
            $data['type']       = 'pesanan';
        } else {
            $data['product_id'] = $this->product_id; // dari select produk siap beli
            $data['quantity']   = $this->quantity;   // input user
            $data['type']       = 'siap beli';
        }

        Production::create($data);
        $this->alert('success', 'Produksi berhasil ditambahkan!');
        $this->resetForm();
        $this->openAdd = false;
    }

    public function editProduction()
    {
        $data = $this->validate();
        $production = Production::findOrFail($this->productionId);

        if ($this->activeTab === 'pesanan') {
            $data['product_id'] = $this->product_id;
            $data['quantity']   = $this->quantity;
            $data['type']       = 'pesanan';
        } else {
            $data['product_id'] = $this->product_id;
            $data['quantity']   = $this->quantity;
            $data['type']       = 'siap beli';
        }

        $production->update($data);
        $this->alert('success', 'Produksi berhasil diperbarui!');
        $this->resetForm();
        $this->openEdit = false;
    }


    public function confirmDelete($id)
    {

        // Simpan ID production ke dalam properti
        $this->delete_id = $id;

        // Konfirmasi menggunakan Livewire Alert
        $this->alert('warning', 'Apakah Anda yakin ingin menghapus produksi ini?', [
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
        $production = Production::findOrFail($this->delete_id);
        $production->delete();

        $this->alert('success', 'Data produksi berhasil dihapus.');
    }

    public function openAddModal()
    {
        $this->resetForm();
        $this->openAdd = true;
    }

    public function openEditModal($id)
    {
        $production = Production::with('product')->findOrFail($id);
        $this->productionId          = $id;
        // Untuk edit, sesuaikan dengan tipe produksi
        if ($production->type === 'pesanan') {
            $this->activeTab = 'pesanan';
            $this->transaction_id = $production->transaction_id;
            $this->transaction_detail_id = $production->transaction_detail_id;
        } else {
            $this->activeTab = 'siap_beli';
        }
        $this->product_id = $production->product_id;
        $this->quantity   = $production->quantity;
        $this->count      = $production->count;
        $this->status     = $production->status;
        $this->time       = $production->time;
        // Ambil komposisi dari produk terkait
        $this->compositionList = $production->product->product_compositions ?? [];
        $this->openEdit = true;
    }

    private function resetForm()
    {
        $this->reset([
            'transaction_id',
            'transaction_detail_id',
            'product_id',
            'quantity',
            'count',
            'status',
            'time',
            'compositionList',
            'productionId',
            'activeTab'
        ]);
        // Default kembali ke tab Pesanan
        $this->activeTab = 'pesanan';
        $this->resetValidation();
    }
}

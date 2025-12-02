<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\PointsHistory;
use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

class Show extends Component
{
    use LivewireAlert, WithFileUploads, WithPagination;

    public $customerId;

    public $name = '';

    public $phone = '';

    public $lastTransaction;

    public $points = 0;

    public $totalTransactions = 0;

    public $totalPayment = 0;

    public $addPointsModal = false;

    public $ig_image = null;

    public $gmaps_image = null;

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $historySearch = '';

    public $historySortField = 'created_at';

    public $historySortDirection = 'desc';

    public $orderSearch = '';

    public $orderSortField = 'created_at';

    public $orderSortDirection = 'desc';

    public $orderType = 'all';

    protected $listeners = [
        'delete' => 'delete',
        'cancelled' => 'cancelled',
    ];

    public function mount($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->points = $customer->points;
        $this->lastTransaction = $customer->transactions()->latest()->first()?->created_at;
        $this->totalTransactions = $customer->transactions()->count();
        $this->totalPayment = $customer->transactions()->sum('final_price');

        View::share('title', 'Rincian Pelanggan');
        View::share('mainTitle', 'Pelanggan');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'phone' => 'required|string|unique:customers,phone,' . $this->customerId,
        ], [
            'name.required' => 'Nama pelanggan harus diisi.',
            'phone.required' => 'Nomor telepon pelanggan harus diisi.',
            'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
        ]);

        $customer = Customer::find($this->customerId);
        if ($customer) {
            $customer->update([
                'name' => $this->name,
                'phone' => $this->phone,
            ]);

            $this->alert('success', 'Pelanggan berhasil diperbarui.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } else {
            $this->alert('error', 'Pelanggan tidak ditemukan.');
        }
    }

    public function confirmDelete()
    {
        $this->alert('question', 'Apakah Anda yakin ingin menghapus pelanggan ini?', [
            'showConfirmButton' => true,
            'confirmButtonText' => 'Hapus',
            'showCancelButton' => true,
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'toast' => false,
            'position' => 'center',
        ]);
    }

    public function delete()
    {
        $customer = Customer::find($this->customerId);

        if ($customer) {
            $customer->delete();
            $this->alert('success', 'Pelanggan berhasil dihapus!');

            return redirect()->route('customer');
        } else {
            $this->alert('error', 'Pelanggan tidak ditemukan!');
        }
    }

    public function cancelled()
    {
        $this->alert('info', 'Penghapusan dibatalkan.');
    }

    public function showModalTambahPoin()
    {
        $this->reset(['ig_image', 'gmaps_image']);
        $this->addPointsModal = true;
    }

    public function updatedIgImage()
    {
        $this->validate([
            'ig_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }

    public function updatedGmapsImage()
    {
        $this->validate([
            'gmaps_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }

    public function addPoints()
    {
        $this->validate([
            'ig_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'gmaps_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $customer = Customer::find($this->customerId);

        if ($customer) {
            $points = 0;

            if ($this->ig_image instanceof \Illuminate\Http\UploadedFile) {
                $points += 5;
                $pointsHistoryIg = PointsHistory::create([
                    'phone' => $customer->phone,
                    'action' => 'Story Instagram',
                    'points' => 5,
                ]);
                $pointsHistoryIg->image = $this->ig_image->store('points/ig', 'public');
                $pointsHistoryIg->save();
            }

            if ($this->gmaps_image instanceof \Illuminate\Http\UploadedFile) {
                $points += 10;
                $pointsHistoryGmaps = PointsHistory::create([
                    'phone' => $customer->phone,
                    'action' => 'Rating Gmaps',
                    'points' => 10,
                ]);
                $pointsHistoryGmaps->image = $this->gmaps_image->store('points/gmaps', 'public');
                $pointsHistoryGmaps->save();
            }

            $customer->points += $points;
            $customer->save();
            $this->points = $customer->points;
            $this->reset(['ig_image', 'gmaps_image']);
            $this->addPointsModal = false;

            $this->alert('success', "Poin berhasil ditambahkan: {$points} poin.");
        } else {
            $this->alert('error', 'Pelanggan tidak ditemukan!');
        }
    }

    public function sortHistoryBy($field)
    {
        if ($this->historySortField === $field) {
            $this->historySortDirection = $this->historySortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->historySortField = $field;
            $this->historySortDirection = 'asc';
        }
    }

    public function sortOrderBy($field)
    {
        if ($this->orderSortField === $field) {
            $this->orderSortDirection = $this->orderSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orderSortField = $field;
            $this->orderSortDirection = 'asc';
        }
    }

    public function setOrderType($type)
    {
        $this->orderType = $type;
        $this->resetPage('ordersPage');
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('customers')
            ->where('subject_id', $this->customerId)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function getPointsHistoriesProperty()
    {
        return PointsHistory::where('phone', $this->phone)
            ->orderBy($this->historySortField, $this->historySortDirection)
            ->paginate(6, ['*'], 'historyPage');
    }

    public function getOrdersProperty()
    {
        $query = Transaction::where('phone', $this->phone);

        if ($this->orderType !== 'all') {
            $query->where('method', $this->orderType);
        }

        if ($this->orderSearch) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->orderSearch . '%')
                    ->orWhereHas('details', function ($q) {
                        $q->whereHas('product', function ($q) {
                            $q->where('name', 'like', '%' . $this->orderSearch . '%');
                        });
                    });
            });
        }

        return $query->orderBy($this->orderSortField, $this->orderSortDirection)
            ->paginate(5, ['*'], 'ordersPage');
    }

    public function getTopProductsProperty()
    {
        return Transaction::where('phone', $this->phone)
            ->with('details.product')
            ->get()
            ->flatMap(fn($transaction) => $transaction->details)
            ->groupBy(fn($detail) => $detail->product?->name ?? 'Unknown')
            ->map(fn($group, $name) => [
                'name' => $name,
                'quantity' => $group->sum('quantity'),
            ])
            ->sortByDesc('quantity')
            ->take(10)
            ->values();
    }

    public function render()
    {
        return view('livewire.customer.show', [
            'histories' => $this->pointsHistories,
            'orders' => $this->orders,
            'topProducts' => $this->topProducts,
        ]);
    }
}

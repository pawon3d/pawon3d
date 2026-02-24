<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\Payment;
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

    public ?string $customerId = null;

    public string $name = '';

    public string $phone = '';

    public mixed $lastTransaction = null;

    public int $points = 0;

    public int $totalTransactions = 0;

    public float $totalPayment = 0;

    public bool $addPointsModal = false;

    public mixed $ig_image = null;

    public mixed $gmaps_image = null;

    public bool $showHistoryModal = false;

    public bool $showPaymentModal = false;

    public array $payments = [];

    public array $refunds = [];

    public array $cancellations = [];

    public float $totalPaidInModal = 0;

    public float $totalRefundInModal = 0;

    public float $netPaidInModal = 0;

    public array $activityLogs = [];

    public string $historySearch = '';

    public string $historySortField = 'created_at';

    public string $historySortDirection = 'desc';

    public string $orderSearch = '';

    public string $orderSortField = 'created_at';

    public string $orderSortDirection = 'desc';

    public string $orderType = 'pesanan-reguler';

    protected $listeners = [
        'delete' => 'delete',
        'cancelled' => 'cancelled',
    ];

    public function mount(string $id): void
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->points = $customer->points;
        $this->lastTransaction = $customer->transactions()->latest()->first()?->created_at;
        $this->totalTransactions = $customer->transactions()->count();

        // Calculate total paid amount by summing payments related to this customer's transactions.
        // A transaction can be linked to a customer via customer_id or by matching phone number.
        $this->totalPayment = Payment::whereHas('transaction', function ($q) use ($customer) {
            $q->where('customer_id', $customer->id)
                ->orWhere('phone', $customer->phone);
        })->sum('paid_amount');

        View::share('title', 'Rincian Pelanggan');
        View::share('mainTitle', 'Pelanggan');
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'phone' => 'required|string|unique:customers,phone,'.$this->customerId,
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

    public function confirmDelete(): void
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

    public function delete(): mixed
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

    public function cancelled(): void
    {
        $this->alert('info', 'Penghapusan dibatalkan.');
    }

    public function showModalTambahPoin(): void
    {
        $this->reset(['ig_image', 'gmaps_image']);
        $this->addPointsModal = true;
    }

    public function updatedIgImage(): void
    {
        $this->validate([
            'ig_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }

    public function updatedGmapsImage(): void
    {
        $this->validate([
            'gmaps_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    }

    public function addPoints(): void
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

    public function sortHistoryBy(string $field): void
    {
        if ($this->historySortField === $field) {
            $this->historySortDirection = $this->historySortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->historySortField = $field;
            $this->historySortDirection = 'asc';
        }
    }

    public function sortOrderBy(string $field): void
    {
        if ($this->orderSortField === $field) {
            $this->orderSortDirection = $this->orderSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orderSortField = $field;
            $this->orderSortDirection = 'asc';
        }
    }

    public function setOrderType(string $type): void
    {
        $this->orderType = $type;
        $this->resetPage('ordersPage');
    }

    public function riwayatPembaruan(): void
    {
        $this->activityLogs = Activity::inLog('customers')
            ->where('subject_id', $this->customerId)
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function showDetailModal(): void
    {
        // Load all payments for transactions that belong to this customer (by customer_id or phone)
        $this->payments = Payment::whereHas('transaction', function ($q) {
            $q->where('customer_id', $this->customerId)
                ->orWhere('phone', $this->phone);
        })->with(['transaction', 'channel'])->orderBy('paid_at', 'desc')->get();

        // Load refunds related to this customer's transactions
        $this->refunds = \App\Models\Refund::whereHas('transaction', function ($q) {
            $q->where('customer_id', $this->customerId)
                ->orWhere('phone', $this->phone);
        })->with(['transaction', 'channel'])->orderBy('refunded_at', 'desc')->get();

        // Load cancelled transactions
        $this->cancellations = \App\Models\Transaction::where(function ($q) {
            $q->where('customer_id', $this->customerId)
                ->orWhere('phone', $this->phone);
        })->whereNotNull('cancelled_at')->orderBy('cancelled_at', 'desc')->get();

        $this->totalPaidInModal = collect($this->payments)->sum('paid_amount');
        $this->totalRefundInModal = collect($this->refunds)->sum('total_amount');
        $this->netPaidInModal = $this->totalPaidInModal - $this->totalRefundInModal;

        $this->showPaymentModal = true;
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
                $q->where('id', 'like', '%'.$this->orderSearch.'%')
                    ->orWhereHas('details', function ($q) {
                        $q->whereHas('product', function ($q) {
                            $q->where('name', 'like', '%'.$this->orderSearch.'%');
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
            ->flatMap(fn ($transaction) => $transaction->details)
            ->groupBy(fn ($detail) => $detail->product?->name ?? 'Unknown')
            ->map(fn ($group, $name) => [
                'name' => $name,
                'quantity' => $group->sum('quantity'),
            ])
            ->sortByDesc('quantity')
            ->take(10)
            ->values();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.customer.show', [
            'histories' => $this->pointsHistories,
            'orders' => $this->orders,
            'topProducts' => $this->topProducts,
        ]);
    }
}

<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\PointsHistory;
use Flux\Flux;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use LivewireAlert, WithFileUploads;

    public $search = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $showHistoryModal = false;

    public $activityLogs = [];

    public $filterStatus;

    public $customerModal = false;

    public $customerDetailModal = false;

    public $addPointsModal = false;

    public $customerId;

    public $name = '';

    public $phone = '';

    public $lastTransaction;

    public $points = 0;

    public $totalTransactions = 0;

    public $histories = [];

    public $ig_image = null;

    public $gmaps_image = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    protected $messages = [
        'name.required' => 'Nama pelanggan harus diisi.',
        'phone.required' => 'Nomor telepon pelanggan harus diisi.',
        'phone.unique' => 'Nomor telepon ini sudah terdaftar.',
    ];

    public function mount()
    {
        View::share('title', 'Pelanggan');
        View::share('mainTitle', 'Pelanggan');
    }

    public function riwayatPembaruan()
    {
        $this->activityLogs = Activity::inLog('customers')
            ->latest()
            ->limit(50)
            ->get();

        $this->showHistoryModal = true;
    }

    public function showModalTambah()
    {
        $this->reset(['customerId', 'name', 'phone']);
        $this->customerModal = true;
    }

    public function addCustomer()
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'phone' => 'required|string|unique:customers,phone',
        ]);

        \App\Models\Customer::create([
            'name' => $this->name,
            'phone' => $this->phone,
        ]);

        $this->customerModal = false;
        $this->alert('success', 'Pelanggan berhasil ditambahkan.', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'phone' => 'required|string|unique:customers,phone,' . $this->customerId,
        ]);

        $customer = Customer::find($this->customerId);
        if ($customer) {
            $customer->update([
                'name' => $this->name,
                'phone' => $this->phone,
            ]);

            $this->customerDetailModal = false;
            $this->alert('success', 'Pelanggan berhasil diperbarui.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } else {
            $this->alert('error', 'Pelanggan tidak ditemukan.');
        }
    }

    public function showCustomerDetail($id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $this->customerId = $customer->id;
        $this->name = $customer->name;
        $this->phone = $customer->phone;
        $this->points = $customer->points;
        $this->lastTransaction = $customer->transactions()->latest()->first()->created_at ?? null;
        $this->totalTransactions = $customer->transactions()->count();

        $this->histories = $customer->pointsHistories()->latest()->get();

        $this->customerDetailModal = true;
    }

    public function delete()
    {

        $customer = Customer::find($this->customerId);

        if ($customer) {
            $customer->delete();
            $this->alert('success', 'Pelanggan berhasil dihapus!');
            $this->reset('customerId');
            Flux::modals()->close();
        } else {
            $this->alert('error', 'Pelanggan tidak ditemukan!');
        }
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
            $this->reset(['ig_image', 'gmaps_image']);
            $this->addPointsModal = false;
            $histories = $customer->pointsHistories()->latest()->get();
            $this->histories = $histories;

            $this->alert('success', "Poin berhasil ditambahkan: {$points} poin.");
        } else {
            $this->alert('error', 'Pelanggan tidak ditemukan!');
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        return view('livewire.customer.index', [
            'customers' => \App\Models\Customer::query()
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}

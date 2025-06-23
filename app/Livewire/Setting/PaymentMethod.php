<?php

namespace App\Livewire\Setting;

use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PaymentMethod extends Component
{
    use \Livewire\WithPagination, \Livewire\WithFileUploads, LivewireAlert;
    public $sortDirection = 'desc';
    public $sortField = 'created_at';
    public $bankName, $type, $group, $accountNumber, $accountName, $isActive = true;
    public $qrisImage;
    public $showModal = false;
    public $edit = false;
    public $paymentChannelId;

    public function mount()
    {
        View::share('title', 'Metode Pembayaran');
        View::share('mainTitle', 'Pengaturan');
    }

    public function resetFields()
    {
        $this->bankName = '';
        $this->type = '';
        $this->group = '';
        $this->accountNumber = '';
        $this->accountName = '';
        $this->isActive = true;
        $this->qrisImage = null;
        $this->showModal = false;
        $this->edit = false;
        $this->paymentChannelId = null;
    }
    public function openModal($edit = false, $paymentChannelId = null)
    {
        $this->resetFields();
        $this->edit = $edit;
        $this->paymentChannelId = $paymentChannelId;

        if ($edit && $paymentChannelId) {
            $channel = \App\Models\PaymentChannel::find($paymentChannelId);
            if ($channel) {
                $this->bankName = $channel->bank_name ?? '';
                $this->type = $channel->type ?? '';
                $this->group = $channel->group ?? '';
                $this->accountNumber = $channel->account_number ?? '';
                $this->accountName = $channel->account_name ?? '';
                $this->isActive = $channel->is_active ?? true;
                // Assuming qris_image is a file path
                $this->qrisImage = $channel->qris_image ?? null;
            }
        }

        $this->showModal = true;
    }
    public function save()
    {
        $this->validate([
            'bankName' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'accountNumber' => 'required|string|max:50',
            'accountName' => 'required|string|max:255',
            'isActive' => 'boolean',
            'qrisImage' => 'nullable|file|max:2048',
        ]);
        if ($this->group == 'tunai') {
            $this->type = 'tunai';
        } elseif ($this->group == 'qris') {
            $this->type = 'qris';
        } else {
            $this->type = 'transfer';
        }

        if ($this->edit && $this->paymentChannelId) {
            $channel = \App\Models\PaymentChannel::find($this->paymentChannelId);
            if ($channel) {
                $channel->update([
                    'bank_name' => $this->bankName,
                    'type' => $this->type,
                    'group' => $this->group,
                    'account_number' => $this->accountNumber,
                    'account_name' => $this->accountName,
                    'is_active' => $this->isActive,
                ]);
                if ($this->qrisImage) {
                    // If a new QRIS image is uploaded, store it and update the channel
                    if ($channel->qris_image) {
                        // Optionally delete the old image if needed
                        Storage::disk('public')->delete($channel->qris_image);
                    }
                    // Store the new QRIS image
                    $channel->qris_image = $this->qrisImage->store('qris_images', 'public');
                    $channel->save();
                }
                $this->alert('success', 'Metode pembayaran berhasil diperbarui.');
            }
        } else {
            $channel = \App\Models\PaymentChannel::create([
                'bank_name' => $this->bankName,
                'type' => $this->type,
                'group' => $this->group,
                'account_number' => $this->accountNumber,
                'account_name' => $this->accountName,
                'is_active' => $this->isActive,
            ]);
            if ($this->qrisImage) {
                // Store the QRIS image
                $channel->qris_image = $this->qrisImage->store('qris_images', 'public');
                $channel->save();
            }
            $this->alert('success', 'Metode pembayaran berhasil ditambahkan.');
        }

        $this->resetFields();
    }
    public function delete()
    {
        $channel = \App\Models\PaymentChannel::find($this->paymentChannelId);
        if ($channel) {
            $channel->delete();
            $this->alert('success', 'Metode pembayaran berhasil dihapus.');
        } else {
            $this->alert('error', 'Metode pembayaran tidak ditemukan.');
        }
        Flux::modals()->close();
        $this->resetFields();
    }
    public function render()
    {
        return view('livewire.setting.payment-method', [
            'paymentChannels' => \App\Models\PaymentChannel::orderBy($this->sortField, $this->sortDirection)->paginate(10),
        ]);
    }
}

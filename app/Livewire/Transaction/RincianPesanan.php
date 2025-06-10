<?php

namespace App\Livewire\Transaction;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Livewire\Component;

class RincianPesanan extends Component
{
    use \Jantinnerezo\LivewireAlert\LivewireAlert, \Livewire\WithFileUploads;

    public $transactionId;
    public $details = [];
    public $paymentMethod = '', $paymentTarget = '', $paymentAccount, $image;
    public $totalAmount = 0;
    public $paidAmount = 0;
    public $transaction;
    public $total_quantity_plan, $total_quantity_get, $percentage;
    public $showPrintModal = false;
    public function mount($id)
    {
        View::share('title', 'Rincian Pesanan');

        if (session()->has('success')) {
            $this->alert('success', session('success'));
        }
        $this->transactionId = $id;
        $transaction = \App\Models\Transaction::with(['details', 'user'])->find($id);
        $this->transaction = $transaction;
        if ($transaction) {
            $this->details = $transaction->details->mapWithKeys(function ($detail) {
                return [
                    (string) $detail->product_id => [
                        'product_id' => $detail->product_id,
                        'quantity' => $detail->quantity,
                        'price' => $detail->price,
                        'name' => $detail->product->name,
                        'stock' => $detail->product->stock,
                    ],
                ];
            })->toArray();
            $this->totalAmount = $transaction->total_amount;
            $this->paidAmount = $transaction->paid_amount;
            $this->paymentMethod = $transaction->payment_method;
            $this->paymentTarget = $transaction->payment_target;
            $this->paymentAccount = $transaction->payment_account;
            $this->image = $transaction->image;
            $this->total_quantity_plan = $this->transaction->details->sum('quantity');
            $this->total_quantity_get = 0;
            $this->percentage = $this->total_quantity_plan > 0 ? ($this->total_quantity_get / $this->total_quantity_plan) * 100 : 0;
            if ($this->percentage > 100) {
                $this->percentage = 100;
            }
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
            return redirect()->route('transaksi');
        }
    }

    public function updatedPaidAmount($value)
    {
        $this->paidAmount = $value;
    }

    public function updatedPaymentTarget($value)
    {
        if ($value === 'BRI') {
            $this->paymentAccount = 'BRI - 0912389103';
        } elseif ($value === 'BCA') {
            $this->paymentAccount = 'BCA - 0912389103';
        } else {
            $this->paymentAccount = 'Mandiri - 0912389103';
        }
    }

    public function pay()
    {
        $this->validate([
            'paymentMethod' => 'nullable|string',
            'paymentTarget' => 'nullable|string',
            'paymentAccount' => 'nullable|string',
        ]);

        $transaction = \App\Models\Transaction::find($this->transactionId);
        if ($transaction) {
            $transaction->update([
                'status' => 'Belum Diproses',
                'paid_amount' => $this->paidAmount,
                'payment_method' => $this->paymentMethod,
                'payment_target' => $this->paymentTarget,
                'payment_account' => $this->paymentAccount,
                'payment_status' => $this->paidAmount >= $this->totalAmount ? 'Lunas' : 'Belum Lunas',
            ]);

            if ($this->image instanceof \Illuminate\Http\UploadedFile) {
                // hapuskan gambar lama jika ada
                if ($transaction->image) {
                    Storage::disk('public')->delete($transaction->image);
                }
                $path = $this->image->store('payments', 'public');
                $transaction->update(['image' => $path]);
            }

            session()->flash('success', 'Pesanan berhasil dibuat.');
        } else {
            session()->flash('error', 'Transaksi tidak ditemukan.');
        }

        $this->showPrintModal = true;
        $this->alert('success', 'Pembayaran berhasil diproses!');
    }

    public function render()
    {
        return view('livewire.transaction.rincian-pesanan');
    }
}
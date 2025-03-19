<?php

namespace App\Livewire\Review;

use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ReviewForm extends Component
{

    use LivewireAlert;

    public $name;
    public $ratings = [];
    public $comments = [];
    public $transaction;
    public $showError = false;
    public $isLoading = false;
    public $errorMessage = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'ratings.*' => 'required|integer|min:1|max:5',
        'comments.*' => 'required|string|max:500',
    ];

    protected $listeners = [
        'redirectToHome',
    ];

    public function mount($transaction_id)
    {
        View::share('title', 'Ulasan');
        $this->transaction = Transaction::with(['details.product'])
            ->find($transaction_id);

        if (!$this->transaction) {
            $this->errorMessage = 'Transaksi tidak ditemukan!';
            $this->showError = true;
        } else {
            // Initialize ratings dan comments
            foreach ($this->transaction->details as $detail) {
                $this->ratings[$detail->product->id] = '';
                $this->comments[$detail->product->id] = '';
            }
        }
    }


    public function submit()
    {
        $this->validate();
        $this->isLoading = true;

        try {
            foreach ($this->transaction->details as $detail) {
                Review::create([
                    'transaction_id' => $this->transaction->id,
                    'product_id' => $detail->product->id,
                    'name' => $this->name,
                    'rating' => $this->ratings[$detail->product->id],
                    'comment' => $this->comments[$detail->product->id],
                ]);
            }

            $this->alert('success', 'Review berhasil dikirim!', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
                'text' =>  '',
                'showCancelButton' =>  false,
                'showConfirmButton' =>  true,
                'confirmButtonText' =>  'OK',
                'onConfirmed' =>  'redirectToHome',
                'onDismissed' =>  'redirectToHome',
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Terjadi kesalahan. Coba lagi nanti.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function redirectToHome()
    {
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.review.review-form');
    }
}

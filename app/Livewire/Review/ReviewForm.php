<?php

namespace App\Livewire\Review;

use App\Models\Prize;
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
    public $showModal = false;
    public $isLoading = false;
    public $errorMessage = '';
    public $prizeMessage;
    public $prizeCode;


    protected $rules = [
        'name' => 'required|string|max:255',
        'ratings.*' => 'required|integer|min:1|max:5',
        'comments.*' => 'required|string|max:500',
    ];

    protected $listeners = [
        'redirectToHome',
        'drawPrize',
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

            $this->showModal();
        } catch (\Exception $e) {
            $this->alert('error', 'Terjadi kesalahan. Coba lagi nanti.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function showModal()
    {
        $this->reset('prizeMessage', 'prizeCode');
        $this->showModal = true;
        $this->dispatch('modal-opened');
    }

    public function drawPrize()
    {
        $undian = rand(1, 100);
        $prizes = Prize::where('is_get', false)->with('product')->get();

        if ($prizes->isEmpty()) {
            $this->prizeMessage = 'Maaf, hadiah sudah habis.';
        } elseif ($undian <= 20 && $prizes->isNotEmpty()) {
            $selectedPrize = $prizes->random();
            $selectedPrize->update(['is_get' => true]);
            $this->prizeMessage = 'Selamat, Anda mendapatkan ' . $selectedPrize->product->name;
            $this->prizeCode = $selectedPrize->code;
        } elseif ($undian > 20) {
            $this->prizeMessage = 'Maaf, Anda belum beruntung.';
        }
    }

    public function closeModal()
    {
        $this->reset('prizeMessage', 'prizeCode', 'name', 'ratings', 'comments');
        $this->showModal = false;
        $this->redirectToHome();
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

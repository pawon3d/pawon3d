<div>
    @if($showError)
    <div class="text-center mt-8" x-data="{ showMessage: true }"
        x-init="setTimeout(() => { showMessage = false; window.location.href = '/'; }, 2000)" x-show="showMessage">
        <p class="text-red-500 font-semibold">
            {{ $errorMessage }} Mengarahkan ke halaman utama dalam 3 detik...
        </p>
    </div>
    @else
    <main class="min-h-screen bg-white p-6">
        <div class="max-w-2xl mx-auto bg-white p-6  ">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">
                Form Testimoni
            </h1>

            <form wire:submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Kode Transaksi:
                    </label>
                    <input type="text" value="{{ $transaction->id }}" readonly
                        class="block w-full px-3 py-2 bg-gray-200 rounded-md text-gray-800" />
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Nama:
                    </label>
                    <input type="text" wire:model="nama"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md"
                        placeholder="Masukkan nama Anda" required />
                    @error('nama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @foreach($transaction->details as $detail)
                <div class="mb-6" wire:key="{{ $detail->product->id }}">
                    <h4 class="text-lg font-semibold mb-2 text-gray-700">
                        {{ $detail->product->name }}
                    </h4>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Rating:
                        </label>
                        <div class="flex space-x-1">
                            @for ($i = 1; $i <= 5; $i++) <button type="button"
                                wire:click="$set('ratings.{{ $detail->product->id }}', {{ $i }})"
                                class="{{ (isset($ratings[$detail->product->id]) && $ratings[$detail->product->id] >= $i) ? 'text-yellow-500' : 'text-gray-400' }}">
                                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M10 15l-5.878 3.09 1.122-6.545L.488 6.91l6.564-.955L10 0l2.948 5.955 6.564.955-4.756 4.635L15.878 18z" />
                                </svg>
                                </button>
                                @endfor
                        </div>
                        @error("ratings.{$detail->product->id}")
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Komentar:
                        </label>
                        <textarea wire:model="comments.{{ $detail->product->id }}" rows="3"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md"
                            placeholder="Tulis komentar untuk {{ $detail->product->name }}" required></textarea>
                        @error("comments.{$detail->product->id}")
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @endforeach


                <button type="submit"
                    class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 disabled:opacity-50"
                    wire:loading.attr="disabled">
                    @if($isLoading)
                    Mengirim...
                    @else
                    Kirim
                    @endif
                </button>
            </form>
        </div>
    </main>
    @endif
</div>
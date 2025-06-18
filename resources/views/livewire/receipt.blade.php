<div wire:ignore.self>
    @include('partials.head')
    <div class="fixed inset-0 top-0 bottom-0 overflow-y-scroll bg-gray-100/95 z-50" id="struk-loading">
        <div class="w-full px-4">
            <div class="relative min-h-screen pb-32">
                <div class="fixed top-2 right-4 z-50">
                    <flux:button type="button" icon="x-mark" wire:click="kembali" variant="ghost" />
                </div>
                <div class="mx-auto mt-20 max-w-sm text-center fade-slide-up" id="success-content">
                    <div class="state-container">
                        <span id="state" class="state-span active">
                            <svg id="state-svg" width="120" height="120" viewBox="0 0 120 120">
                                <circle id="extra-outer-circle" cx="60" cy="60" r="0" fill="#B9EBC6"
                                    opacity="0" />
                                <circle id="outer-circle" cx="60" cy="60" r="0" fill="#72CF81"
                                    opacity="0" />
                                <circle id="mid-circle" cx="60" cy="60" r="0" fill="#48A457"
                                    opacity="0" />
                                <circle class="pulse-circle" cx="60" cy="60" r="30" fill="#398345" />
                                <path class="checkmark-path" d="M43 64L55 76L87 38" stroke="white" stroke-width="4"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>


                    <p class="text-lg font-bold">
                        Pembayaran Berhasil
                    </p>
                    <p class="text-lg">
                        {{ \Carbon\Carbon::now()->format('d M Y, H:i:s') }} WIB
                    </p>
                </div>
                <div class="max-w-sm mt-8 mb-4 mx-auto border-gray-300 bg-white text-sm rounded-lg shadow-md p-4 font-sans fade-slide-up"
                    id="receipt">
                    <div class="text-center mb-4">
                        <h1 class="text-2xl font-bold pasifico-regular">Pawon3D</h1>
                        <p>Jl. Jenderal Sudirman Km.3 RT.25 RW.07</p>
                        <p>Kel. Muara Bulian, Kec. Muara Bulian, Kab. Batang Hari, Jambi, 36613</p>
                        <p>081122334455</p>
                        <p class="text-sm">www.pawon3d.my.id</p>
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    <div class="space-y-1 mb-4">
                        <p><span class="font-medium">Tanggal Pembayaran:</span>
                            {{ $transaction->start_date ? \Carbon\Carbon::parse($transaction->start_date)->format('d-m-Y H:i') : '-' }}
                        </p>
                        <p><span class="font-medium">No. Pesanan:</span> {{ $transaction->invoice_number }}</p>
                        <p><span class="font-medium">Status Pembayaran:</span> <span class="font-semibold">
                                {{ $transaction->payment_status ?? 'Belum Lunas' }}
                            </span>
                        </p>
                        <p><span class="font-medium">Kasir:</span> {{ $transaction->user->name ?? '-' }}</p>
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    {{-- Daftar Produk --}}
                    <div class="space-y-3 mb-4">
                        @foreach ($transaction->details as $detail)
                            <div>
                                <p class="flex justify-between">
                                    <span>{{ $detail->product->name }}</span><span>Rp{{ number_format($detail->product->price * $detail->quantity, 0, ',', '.') }}</span>
                                </p>
                                <p class="text-gray-500">{{ $detail->quantity }} x
                                    Rp{{ number_format($detail->product->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    {{-- Total --}}
                    <div class="space-y-1 mb-4">
                        <div class="w-full flex flex-col border-b border-gray-200">
                            <div class="flex flex-row justify-between w-full">
                                <p class="px-4 py-2 text-sm text-gray-500">Subtotal {{ count($details) }} Produk</p>
                                <p class="px-4 py-2 text-sm text-gray-500">
                                    Rp{{ number_format($totalAmount, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="flex flex-row justify-between w-full">
                                <p class="px-4 py-2 text-sm text-gray-500 font-bold">Total Tagihan</p>
                                <p class="px-4 py-2 text-sm text-gray-500 font-bold">
                                    Rp{{ number_format($totalAmount, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-dashed pt-2 mb-2"></div>

                    {{-- Pembayaran --}}
                    <div class="space-y-1 mb-4">
                        <div class="flex flex-row justify-between w-full">
                            <p class="px-4 py-2 text-sm text-gray-500">Pembayaran</p>
                            <p class="px-4 py-2 text-sm text-gray-500">
                                @if ($transaction->payment_status == 'Lunas')
                                    Rp{{ number_format($totalAmount, 0, ',', '.') }}
                                @else
                                    Rp{{ !empty($totalPayment) ? number_format($totalPayment, 0, ',', '.') : '0' }}
                                @endif
                            </p>
                        </div>
                        @if ($payments && $payments->count())
                            @foreach ($payments as $index => $payment)
                                @php
                                    $paidAt = $payment->paid_at
                                        ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y')
                                        : '-';
                                    $method = $payment->payment_method ? ucfirst($payment->payment_method) : '-';
                                    $bank = $payment->channel->bank_name ?? null;
                                    $label = $method . ($bank ? ' - ' . ucfirst($bank) : '');
                                @endphp

                                <div class="grid grid-cols-2 gap-4 w-full py-2 border-b">
                                    {{-- Kiri: Info Metode + Tanggal + Bukti --}}
                                    <div class="flex flex-col text-sm text-gray-600 gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-4 py-2">{{ $paidAt }}</span>
                                            <span
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">{{ $label }}</span>

                                            @if ($payment->payment_method && $payment->payment_method !== 'tunai')
                                                <button class="text-gray-500 text-sm underline ml-2"
                                                    wire:click="showImageModal('{{ $payment->id }}')">
                                                    Lihat Bukti
                                                </button>
                                            @endif
                                        </div>
                                        @if ($payments->count() == 1)
                                            @if ($payment->payment_method == 'tunai' && $kembalian > 0)
                                                <div class="flex flex-col text-sm text-gray-600 gap-1 mt-1">
                                                    <div class="flex items-center gap-2">
                                                        <pre class="px-4 py-2">        </pre>
                                                        <span class="px-4 py-2">Bayar</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <pre class="px-4 py-2">        </pre>
                                                        <span class="px-4 py-2">Kembalian</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @elseif ($payments->count() > 1)
                                            @if ($payment->id == $pembayaranKedua->id && $payment->payment_method == 'tunai' && $kembalian > 0)
                                                <div class="flex flex-col text-sm text-gray-600 gap-1 mt-1">

                                                    <div class="flex items-center gap-2">
                                                        <pre class="px-4 py-2">        </pre>
                                                        <span class="px-4 py-2">Bayar</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <pre class="px-4 py-2">        </pre>
                                                        <span class="px-4 py-2">Kembalian</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>

                                    {{-- Kanan: Total bayar (hanya untuk pembayaran pertama yang sekarang index ke-1) --}}
                                    @if ($payments->count() == 1)
                                        @if ($payment->payment_method == 'tunai' && $kembalian > 0)
                                            <div class="flex items-start justify-end text-sm text-gray-600">
                                                <div class="flex flex-col px-4 gap-1">
                                                    <span
                                                        class="py-2">Rp{{ number_format($totalAmount, 0, ',', '.') }}</span>
                                                    <span
                                                        class="py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                                                    <span
                                                        class="py-2">Rp{{ number_format($kembalian, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-start justify-end text-sm text-gray-600">
                                                <span
                                                    class="px-4 py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    @elseif ($payments->count() > 1)
                                        @if ($payment->id == $pembayaranKedua->id && $payment->payment_method == 'tunai' && $kembalian > 0)
                                            {{-- Tampilan khusus untuk pembayaran kedua tunai dengan kembalian --}}
                                            <div class="flex items-start justify-end text-sm text-gray-600">
                                                <div class="flex flex-col px-4 gap-1">
                                                    <span
                                                        class="py-2">Rp{{ number_format($sisaPembayaranPertama, 0, ',', '.') }}</span>
                                                    <span
                                                        class="py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                                                    <span
                                                        class="py-2">Rp{{ number_format($kembalian, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Tampilan normal untuk semua pembayaran lainnya --}}
                                            <div class="flex items-start justify-end text-sm text-gray-600">
                                                <span
                                                    class="px-4 py-2">Rp{{ number_format($payment->paid_amount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        <div class="flex flex-row justify-between w-full">
                            <p class="px-4 py-2 text-sm text-red-500 font-bold">Sisa Tagihan</p>
                            <p class="px-4 py-2 text-sm text-red-500 font-bold">
                                Rp{{ number_format($remainingAmount, 0, ',', '.') }}

                            </p>
                        </div>
                    </div>

                    <div class="mt-6 text-center text-sm">
                        <p>Terima kasih telah berbelanja di tempat kami.</p>
                        <p>Semoga hari Anda dipenuhi kebahagiaan dan berkah.</p>
                        <p>Sampai jumpa lagi!</p>
                    </div>
                </div>
                <div class="fixed bottom-0 left-4 right-4 z-51">
                    <div class="max-w-md mt-8 mb-4 mx-auto border-gray-300 bg-white text-sm rounded-lg shadow-md p-4 font-sans fade-slide-up show text-center flex flex-col gap-4"
                        id="buttons">
                        <input type="text" wire:model="phoneNumber"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="contoh: 08123456789" />
                        <div class="grid grid-cols-2 gap-4">
                            <flux:button type="button" wire:click='kembali' class="w-full">
                                Kembali ke Kasir
                            </flux:button>
                            <flux:button type="button" variant="primary" wire:click="send" class="w-full">
                                Kirim Struk Belanja
                            </flux:button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <style>
        .state-container {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
        }

        .pacifico-regular {
            font-family: "Pacifico", cursive;
            font-weight: 500;
            font-style: normal;
        }

        .state-span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition:
                opacity 0.6s cubic-bezier(0.22, 0.61, 0.36, 1),
                transform 0.6s cubic-bezier(0.22, 0.61, 0.36, 1);
            pointer-events: none;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .state-span.active {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
            z-index: 10;
        }

        .state-span.prepare {
            transform: translate(-50%, -50%) scale(0.8);
        }

        /* Animasi untuk centang */
        .checkmark-path {
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
            animation: drawCheck 0.5s forwards 0.3s;
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }

        .pulse-circle {
            transform-origin: center;
            animation: pulse 1.5s cubic-bezier(0.22, 0.61, 0.36, 1) infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        #extra-outer-circle,
        #outer-circle,
        #mid-circle {
            transition: r 0.8s ease, opacity 0.8s ease;
        }

        /* Transisi masuk dari bawah */
        .fade-slide-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .fade-slide-up.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @livewireScripts
    <script defer>
        window.addEventListener('open-wa', event => {
            window.open(event.detail[0].url, '_blank');
        });
    </script>
    <script defer>
        document.addEventListener("DOMContentLoaded", function() {
            const extraOuter = document.getElementById("extra-outer-circle");
            const outer = document.getElementById("outer-circle");
            const mid = document.getElementById("mid-circle");

            const states = [{
                    r1: 0,
                    r2: 0,
                    r3: 0,
                    o: 0
                },
                {
                    r1: 40,
                    r2: 0,
                    r3: 0,
                    o: 0.4
                },
                {
                    r1: 50,
                    r2: 40,
                    r3: 0,
                    o: 0.5
                },
                {
                    r1: 60,
                    r2: 50,
                    r3: 40,
                    o: 0.6
                },
            ];

            let current = 0;

            function updateState() {
                const state = states[current];

                extraOuter.setAttribute("r", state.r1);
                extraOuter.setAttribute("opacity", state.o);

                outer.setAttribute("r", state.r2);
                outer.setAttribute("opacity", state.o);

                mid.setAttribute("r", state.r3);
                mid.setAttribute("opacity", state.o);

                current = (current + 1) % states.length;
            }

            setTimeout(updateState, 1000);
            setInterval(updateState, 2000);
        });
    </script>
    <script defer>
        document.addEventListener("DOMContentLoaded", function() {
            const successContent = document.getElementById('success-content');
            const receipt = document.getElementById('receipt');
            const s = document.getElementById('struk-loading');
            const buttons = document.getElementById('buttons');

            // Sembunyikan dulu
            successContent.classList.remove('show');
            receipt.classList.remove('show');
            buttons.classList.remove('show');

            // Tampilkan success content setelah 400ms
            setTimeout(() => {
                successContent.classList.add('show');
            }, 400);

            // Tampilkan struk setelah 2 detik (biar seperti mendorong ke atas)
            setTimeout(() => {
                receipt.classList.add('show');
                // Gulir ke bawah untuk menampilkan struk
                // s.scrollTo({
                //     top: receipt.offsetTop,
                //     behavior: 'smooth'
                // });
            }, 2000);
            // Tampilkan tombol setelah 3 detik
            setTimeout(() => {
                buttons.classList.add('show');
            }, 3000);
        });
    </script>
</div>

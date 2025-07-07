<div>
    <section class="mx-auto px-4 py-4 w-full">
        <div class="max-w-5xl mx-auto py-12 px-4 text-center">
            <h2 class="text-3xl font-bold font-[cursive] mb-6">Cara Pesan</h2>

            <!-- Tabs -->
            <div class="inline-flex bg-gray-200 rounded-full p-1 mb-10">
                <button
                    class="px-6 py-2 {{ $caraPesan === 'whatsapp' ? 'bg-black text-white' : 'text-black' }} rounded-full focus:outline-none"
                    wire:click="$set('caraPesan', 'whatsapp')">
                    WhatsApp
                </button>
                <button
                    class="px-6 py-2 {{ $caraPesan === 'toko' ? 'bg-black text-white' : 'text-black' }} rounded-full focus:outline-none"
                    wire:click="$set('caraPesan', 'toko')">
                    Langsung di Toko
                </button>
            </div>

            <!-- Konten Cara Pesan -->
            @if ($caraPesan === 'whatsapp')
            <div class="space-y-6 text-white font-medium">

                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-80 text-left">
                        <p class="text-sm">1. Pilih menu <strong>Pesanan Reguler</strong> atau <strong>Pesanan
                                Kotak</strong> di website
                        </p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-1/2 text-left">
                        <p class="text-sm">4. Lakukan <strong>konfirmasi pesanan</strong> dan
                            <strong>pembayaran</strong>, maka pesanan
                            akan dicatat (Pastikan pesanan berada di dalam <strong>wilayah pemesanan</strong>)
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-80 text-left">
                        <p class="text-sm">2. Pilih <strong>satu</strong> atau lebih Produk yang diinginkan</p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-96 text-left">
                        <p class="text-sm">5. Tunggu <strong>waktu pengambilan yang ditetapkan</strong> atau tunggu
                            <strong>kabar dari
                                penjual</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-96 text-left">
                        <p class="text-sm">3. Tekan <strong>Checkout</strong> untuk beralih ke
                            <strong>WhatsApp</strong><br>
                            <span class="text-sm font-normal block mt-1">(Anda dapat melakukan konsultasi pembelian di
                                WhatsApp)</span>
                        </p>
                    </div>
                    <div class="bg-gray-800 rounded-lg px-6 py-4 md:w-96 text-left">
                        <p class="text-sm">6. Datang ke toko untuk <strong>mengambil pesanan</strong>, jika pesanan
                            belum lunas maka
                            lakukan pelunasan dan pesanan dapat diambil oleh pelanggan.</p>
                    </div>
                </div>
            </div>
            @else
            @endif
        </div>

    </section>
</div>
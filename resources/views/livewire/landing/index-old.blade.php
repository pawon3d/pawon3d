<div>
    <!-- Hero Section -->
    <section class="relative w-full h-[800px] bg-[#333333] overflow-hidden">
        <!-- Background overlay -->
        <div class="absolute inset-0 bg-black opacity-40 z-10"></div>
        
        <!-- Hero Content -->
        <div class="relative z-20 h-full flex items-center px-4 md:px-20">
            <div class="max-w-[610px]">
                <p class="text-[24px] font-bold montserrat-regular text-[#74512d] mb-6">
                    {{ ($storeProfile->name) ?? 'Pawon3D' }}
                </p>
                
                <h1 class="text-[64px] leading-[85px] pacifico-regular text-white mb-12">
                    {{ $storeProfile->tagline ?? 'Kue Rumahan Lezat, Sehangat Pelukan Ibu' }}
                </h1>

                <div class="flex gap-6 items-center">
                    <a href="https://wa.me/{{ $storeProfile->contact ?? '628123456789' }}" target="_blank"
                        class="px-9 py-6 bg-[#e9bd8c] rounded-[15px] montserrat-bold text-[20px] text-[#74512d] hover:bg-[#d4a876] transition-all">
                        Pesan Sekarang
                    </a>
                    <a href="#carapesan" class="montserrat-semibold text-[20px] text-white flex items-center gap-2">
                        <span>Lihat</span>
                        <span>Cara Pesan</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Section -->
    <section class="bg-[#fafafa] py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-14 max-w-[872px] mx-auto">
                <h2 class="text-[38px] leading-[60px] pacifico-regular text-[#333333] mb-9">
                    Produk Unggulan Kami
                </h2>
                <p class="text-[16px] montserrat-medium text-[#525252] leading-normal">
                    Pilihan kue yang paling disukai pembeli!<br>
                    Dibuat dari bahan berkualitas dan rasa yang bikin nagih, siap menemani hari-harimu.
                </p>
            </div>

            <div class="flex gap-5 items-center justify-center overflow-x-auto">
                @forelse ($products as $product)
                <div class="flex flex-col gap-4 pb-6 min-w-[234px]">
                    <div class="w-[234px] h-[155px] rounded-[15px] shadow-sm overflow-hidden">
                        @if($product->product_image)
                        <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover">
                        @else
                        <img src="{{ asset('/img/no-img.jpg')}}" alt="{{ $product->name }}"
                            class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="text-center px-4">
                        <h3 class="text-[16px] montserrat-medium text-[#666666] mb-5 line-clamp-2">{{ $product->name }}</h3>
                        <p class="text-[18px] montserrat-semibold text-[#666666]">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-[#666666] text-center">Tidak ada produk unggulan saat ini.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Points Section -->
    <section class="relative py-20 bg-[#fafafa] overflow-hidden">
        <div class="max-w-[572px] mx-auto px-4 text-center relative z-10">
            <h2 class="text-[38px] leading-[60px] pacifico-regular text-[#933c24] mb-6">
                Jadi Pelanggan dan<br>Kumpulkan Poin!
            </h2>
            <div class="text-[16px] montserrat-medium text-[#333333] mb-9 leading-normal">
                <p class="mb-0">Dapatkan <span class="montserrat-bold">1 poin</span> dengan belanja kelipatan <span class="montserrat-bold">Rp10.000</span>.</p>
                <p class="mb-0">Poin juga dapat dikumpulkan melalui review <span class="montserrat-bold">Story</span></p>
                <p><span class="montserrat-bold">Media Sosial</span> dan review di <span class="montserrat-bold">Google Map</span>s.</p>
            </div>
            <button class="px-9 py-6 bg-[#74512d] rounded-[15px] montserrat-semibold text-[20px] text-white hover:bg-[#5d3f23] transition-all">
                Lihat Cara Dapat dan Tukar Poin!
            </button>
        </div>
    </section>

    <!-- Menu Section with Grid -->
    <section class="py-20 w-full bg-[#fafafa]" id="menu">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-15">
                <h2 class="text-[38px] leading-[60px] pacifico-regular text-[#333333]">Daftar Menu</h2>
            </div>
            <!-- Search and Filter -->
            <div class="flex justify-center items-center gap-4 mb-8 max-w-[1150px] mx-auto">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-4 h-4 text-[#666666]" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search"
                        class="w-full py-2.5 pl-10 pr-4 text-[16px] montserrat-medium text-[#959595] border-[0.5px] border-[#666666] rounded-[20px] bg-white focus:ring-0 focus:border-[#666666]"
                        placeholder="Cari Produk">
                </div>
                <button class="flex items-center gap-2 px-2 py-2.5">
                    <svg class="w-6 h-6 text-[#666666]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" />
                    </svg>
                    <span class="text-[16px] montserrat-medium text-[#666666]">Filter</span>
                </button>
            </div>
        </div>
        <div class="max-w-[1150px] mx-auto">
        <div class="flex items-center bg-[#fafafa] rounded-[15px] shadow-sm mb-8 h-[120px]">
            <div
                class="relative w-full py-8 {{ $method === 'pesanan-reguler' ? 'bg-[#222222] text-[#F8F4E1] border-b-4 border-b-[#E9BD8C]' : 'text-gray-800' }} hover:bg-[#222222] hover:text-[#F8F4E1] hover:border-b-4 hover:border-b-[#E9BD8C] transition-colors rounded-lg">
                <input type="radio" name="method" id="pesanan-reguler" value="pesanan-reguler" wire:model.live="method"
                    class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-reguler" class="cursor-pointer">
                    <div class="w-full flex flex-col items-center">
                        <flux:icon icon="cake" class=" size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Reguler</span>
                    </div>
                </label>
            </div>
            <div
                class="relative w-full py-8 {{ $method === 'pesanan-kotak' ? 'bg-[#222222] text-[#F8F4E1] border-b-4 border-b-[#E9BD8C]' : 'text-gray-800' }} hover:bg-[#222222] hover:text-[#F8F4E1] hover:border-b-4 hover:border-b-[#E9BD8C] transition-colors rounded-lg">
                <input type="radio" name="method" id="pesanan-kotak" value="pesanan-kotak" wire:model.live="method"
                    class="absolute opacity-0 w-0 h-0">
                <label for="pesanan-kotak" class="cursor-pointer">
                    <div class="w-full flex flex-col items-center">
                        <flux:icon icon="package-open" class="size-8" />
                        <span class="text-center hidden md:block">Pesanan Kue Kotak</span>
                    </div>
                </label>
            </div>

            <div
                class="relative w-full py-8 {{ $method === 'siap-beli' ? 'bg-[#222222] text-[#F8F4E1] border-b-4 border-b-[#E9BD8C]' : 'text-gray-800' }} hover:bg-[#222222] hover:text-[#F8F4E1] hover:border-b-4 hover:border-b-[#E9BD8C] transition-colors rounded-lg">
                <input type="radio" name="method" id="siap-beli" value="siap-beli" wire:model.live="method"
                    class="absolute opacity-0 w-0 h-0">
                <label for="siap-beli" class="cursor-pointer">
                    <div class=" w-full flex flex-col items-center">
                        <flux:icon icon="dessert" class="size-8" />
                        <span class="text-center hidden md:block">Siap Saji</span>
                    </div>
                </label>
            </div>
        </div>
        </div>
        
        <!-- Category Pills -->
        <div class="mb-10">
            <ul class="flex flex-nowrap gap-4 overflow-x-auto scroll-hide pb-3">
                <li class="flex-shrink-0">
                    <button wire:click="$set('categorySelected', 'semua')"
                        class="px-4 py-2 text-[16px] montserrat-medium border-[0.5px] border-[#666666] {{ $categorySelected == 'semua' ? 'bg-[#666666] text-white' : 'bg-[#fafafa] text-[#666666]' }} rounded-full hover:bg-[#666666] hover:text-white transition-colors whitespace-nowrap">
                        Semua
                    </button>
                </li>

                @foreach ($categories as $category)
                <li class="flex-shrink-0">
                    <button wire:click="$set('categorySelected', '{{ $category->name }}')"
                        class="px-4 py-2 text-[16px] montserrat-medium border-[0.5px] border-[#666666] {{ $categorySelected == $category->name ? 'bg-[#666666] text-white' : 'bg-[#fafafa] text-[#666666]' }} rounded-full hover:bg-[#666666] hover:text-white transition-colors whitespace-nowrap">
                        {{ $category->name }}
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Products Grid -->
        <div class="bg-[#fafafa] rounded-[15px] shadow-sm p-8 mb-10">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @forelse ($exploreProducts as $product)
                <div class="mb-6">
                    <div class="flex justify-center">
                        @if($product->product_image)
                        <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                            class="w-full h-40 rounded-lg border-gray-400 border object-fill">
                        @else
                        <img src="{{ asset('/img/no-img.jpg')}}" alt="{{ $product->name }}"
                            class="w-full h-40 rounded-lg border-gray-400 border object-fill">

                        @endif
                    </div>
                    <div class="p-4 bg-white text-center">
                        <h3 class="text-xs md:text-lg font-semibold text-gray-800 mb-2">{{ $product->name }}
                        </h3>
                        <div class="flex justify-center items-center">
                            <p class="text-blue-600 font-medium text-xs md:text-md">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    Tidak ada produk tersedia di kategori atau metode ini
                </div>
                @endforelse

                <!-- Pagination -->
                <div class="pagination col-span-full mt-4">
                    {{ $exploreProducts->links(data: ['scrollTo' => false]) }}
                </div>
            </div>

        </div>

        <a href="/landing-produk" class="flex justify-center items-center">
            <button
                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                Selengkapnya
            </button>
        </a>
    </section>



    <!-- Contact Section -->
    <section class="bg-blue-50 py-20">
        <div class="mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-transparent p-8">
                <div class="text-center mb-8">
                    <h2 class="text-4xl pacifico-regular font-bold mb-2">Lokasi Toko dan Wilayah Pemesanan</h2>
                </div>
                <div class="flex flex-col gap-6">
                    <div class="w-full flex flex-row gap-4 items-center justify-center">
                        <div class="flex flex-col items-start mb-4 md:mb-0">
                            <h3 class="text-lg font-semibold mb-2">Metode Pengambilan (Saat Ini)</h3>
                            <p class="text-gray-600 text-sm">
                                Ambil Sendiri, belum ada sistem pengantaran.
                            </p>
                        </div>
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!4v1742133851135!6m8!1m7!1sKwaGqQ5eD1Pjwm1wY_m6ng!2m2!1d-1.729048731821646!2d103.2726813742673!3f215.95130838744583!4f1.839142862723449!5f0.7820865974627469"
                            class="w-full h-[350px]" style="border: 0; border-radius: 10px;" allowfullscreen=""
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <div class="flex flex-col items-end text-end mb-4 md:mb-0 ">
                            <h3 class="text-lg font-semibold mb-2">Wilayah Pemesanan (Saat Ini)</h3>
                            <p class="text-gray-600 text-sm">
                                Muara Bulian, Muara Tembesi, Muara Jambi, dan Kota Jambi.
                            </p>
                        </div>
                    </div>
                    <div class="w-full flex flex-col items-center justify-center">
                        <h3 class="text-lg font-semibold mb-2">Rincian Lokasi Toko</h3>
                        <div class="mb-4">
                            <p class="text-sm">Jl. Jenderal Sudirman Km.3, Muara Bulian, Batang Hari, Jambi.
                                Sebelah Puskesmas Muara Bulian</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto px-4 py-16 w-full">
        <div class="max-w-7xl mx-auto flex flex-col gap-12 items-center">
            <!-- Title -->
            <h2 class="text-[38px] pacifico-regular leading-[60px] text-[#333333] text-center">Cara Pesan</h2>

            <!-- Tabs -->
            <div class="flex gap-4 items-center">
                <button
                    class="px-6 py-2.5 {{ $caraPesan === 'whatsapp' ? 'bg-[#74512d] text-white' : 'bg-white text-[#666666] border border-[#666666]' }} rounded-[20px] font-semibold text-base transition-colors montserrat-regular"
                    wire:click="$set('caraPesan', 'whatsapp')">
                    WhatsApp
                </button>
                <button
                    class="px-6 py-2.5 {{ $caraPesan === 'toko' ? 'bg-[#74512d] text-white' : 'bg-white text-[#666666] border border-[#666666]' }} rounded-[20px] font-medium text-base transition-colors montserrat-regular"
                    wire:click="$set('caraPesan', 'toko')">
                    Langsung di Toko
                </button>
            </div>

            <!-- Konten Cara Pesan -->
            @if ($caraPesan === 'whatsapp')
            <div class="flex flex-col md:flex-row gap-6 items-start justify-center w-full max-w-[1053px]">
                <!-- Left Column -->
                <div class="flex flex-col gap-8 flex-1 w-full md:w-auto">
                    <!-- Step 1 -->
                    <div class="bg-[#333333] rounded-[15px] px-8 py-6 shadow-sm">
                        <ol class="list-decimal list-inside montserrat-regular text-[#fafafa] text-lg leading-normal m-0">
                            <li>Pilih menu <span class="font-bold">Pesanan Reguler</span> atau <span class="font-bold">Pesanan Kotak</span> dan lihat <span class="font-bold">daftar menu</span></li>
                        </ol>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-[#333333] rounded-[15px] px-8 py-6 shadow-sm">
                        <ol start="2" class="list-decimal list-inside montserrat-regular text-[#fafafa] text-lg leading-normal m-0">
                            <li>Pilih satu atau lebih <span class="font-bold">produk</span> yang diinginkan, lalu <span class="font-bold">screenshot</span>.</li>
                        </ol>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-[#333333] rounded-[15px] px-8 py-6 shadow-sm">
                        <ol start="3" class="list-decimal list-inside montserrat-regular text-[#fafafa] text-lg leading-normal m-0">
                            <li>Tekan <span class="font-bold">Pesan Sekarang</span> untuk beralih ke <span class="font-bold">WhatsApp</span> dan kirimkan hasil <span class="font-bold">screenshot</span> untuk melakukan <span class="font-bold">konfirmasi pesanan.</span></li>
                        </ol>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="flex flex-col gap-8 flex-1 w-full md:w-auto">
                    <!-- Step 4 -->
                    <div class="bg-[#333333] rounded-[15px] px-8 py-6 shadow-sm">
                        <ol start="4" class="list-decimal list-inside montserrat-regular text-[#fafafa] text-lg leading-normal m-0">
                            <li>Lakukan <span class="font-bold">konfirmasi pesanan</span> dan <span class="font-bold">pembayaran,</span> maka pesanan akan dicatat (pastikan pesanan berada di dalam <span class="font-bold">wilayah pemesanan</span>)</li>
                        </ol>
                    </div>

                    <!-- Step 5 -->
                    <div class="bg-[#333333] rounded-[15px] px-8 py-6 shadow-sm">
                        <ol start="5" class="list-decimal list-inside montserrat-regular text-[#fafafa] text-lg leading-normal m-0">
                            <li>Terima <span class="font-bold">invoice</span> dan ambil pesanan <span class="font-bold">sesuai kesepakatan</span> atau <span class="font-bold">tunggu kabar dari penjual.</span></li>
                        </ol>
                    </div>

                    <!-- Step 6 -->
                    <div class="bg-[#333333] rounded-[15px] px-8 py-6 shadow-sm">
                        <ol start="6" class="list-decimal list-inside montserrat-regular text-[#fafafa] text-lg leading-normal m-0">
                            <li>Datang ke <span class="font-bold">toko</span> atau <span class="font-bold">tunggu pengiriman</span> untuk menerima pesanan, jika pesanan belum lunas maka lakukan pelunasan.</li>
                        </ol>
                    </div>
                </div>
            </div>
            @else
            <!-- Content for "Langsung di Toko" option -->
            <div class="text-center text-gray-600">
                <p class="montserrat-regular text-lg">Konten untuk pemesanan langsung di toko akan ditampilkan di sini.</p>
            </div>
            @endif
        </div>
    </section>


</div>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($storeSetting->store_name) ?? 'Pawon3D' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700"
        rel="stylesheet" />
    <link href="{{ asset('flowbite/flowbite.min.css') }}" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <!-- favicon -->
    @if(!empty($storeSetting->logo))
    <link rel="icon" href="{{ asset('storage/' . $storeSetting->logo) }}" type="image/x-icon" />
    @endif



    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" />
    <style>
        html {
            scroll-behavior: smooth;
        }

        .montserrat-regular {
            font-family: "Montserrat", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .pacifico-regular {
            font-family: "Pacifico", cursive;
            font-weight: 500;
            font-style: normal;
        }
    </style>
</head>

<body class="bg-[#FDFDFC] text-[#1b1b18] montserrat-regular">
    <!-- Enhanced Header -->
    <header class="sticky top-0 bg-white backdrop-blur-sm z-50 shadow-sm">
        <nav class="container mx-auto px-4 py-3 flex items-center">
            <a href="/" class="lg:text-3xl text-lg font-bold text-blue-600 hover:text-blue-700 transition-colors">
                <span class="text-blue-400">{{ $storeSetting->store_name ?? 'Pawon3D' }}</span>
            </a>

            <flux:navbar class="hidden md:flex ml-4 flex-row gap-6">
                <flux:navbar.item href="/" :current="request()->routeIs('home')">Home</flux:navbar.item>
                <flux:navbar.item href="/landing-produk" :current="request()->routeIs('landing-produk*')">Produk
                </flux:navbar.item>
                <flux:navbar.item href="/landing-cara-pesan" :current="request()->routeIs('landing-cara-pesan')">Cara
                    Pesan</flux:navbar.item>
                <flux:navbar.item href="/landing-ulasan" :current="request()->routeIs('landing-ulasan')">Ulasan
                </flux:navbar.item>
            </flux:navbar>
            <div class="hidden md:flex ml-auto">
                @auth
                <a href="{{ url('/dashboard') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-full text-center">Dashboard</a>
                @else
                <a href="{{ route('login') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-full text-center">Login</a>
                @endauth
            </div>
            <flux:button id="nav-toggle" variant="ghost" class="md:hidden ml-auto">
                <flux:icon.bars-2 />
            </flux:button>
        </nav>

        {{-- mobile menu --}}
        <flux:navbar id="nav-menu"
            class="hidden absolute top-full left-0 w-full bg-white flex-col gap-4 px-4 py-3 shadow-md md:hidden">
            <flux:navbar.item href="/" :current="request()->routeIs('home')">Home</flux:navbar.item>
            <flux:navbar.item href="/landing-produk" :current="request()->routeIs('landing-produk*')">Produk
            </flux:navbar.item>
            <flux:navbar.item href="/landing-cara-pesan" :current="request()->routeIs('landing-cara-pesan')">Cara Pesan
            </flux:navbar.item>
            <flux:navbar.item href="/landing-ulasan" :current="request()->routeIs('landing-ulasan')">Ulasan
            </flux:navbar.item>

            {{-- tombol auth --}}
            @auth
            <a href="{{ url('/dashboard') }}" class="block px-4 py-2 bg-blue-600 text-white rounded-full text-center">
                Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="block px-4 py-2 bg-blue-600 text-white rounded-full text-center">
                Login
            </a>
            @endauth
        </flux:navbar>
    </header>


    <main class="">
        <!-- Hero Section with Animation -->
        <section class="px-4 py-20 mb-4 bg-blue-50">
            <div class="flex flex-col lg:flex-row items-center gap-8">
                <div class="lg:w-3/5 space-y-6">
                    <h3 class="text-xl font-semibold text-blue-600 animate-slideInLeft ml-2">
                        {{ ($storeSetting->store_name) ?? 'Pawon3D' }}
                    </h3>
                    @php
                    // Ambil hero title dari storeSetting, jika tidak ada gunakan default
                    $heroTitle = $storeSetting->hero_title ?? 'Kue Rumahan Lezat, Sehangat Pelukan Ibu';

                    // Pecah string menjadi array berdasarkan spasi
                    $words = explode(' ', $heroTitle);
                    $wordCount = count($words);

                    // Jika jumlah kata lebih dari 2, pisahkan menjadi dua bagian
                    if ($wordCount > 2) {
                    $before = implode(' ', array_slice($words, 0, -2)); // Semua kata kecuali 2 kata terakhir
                    $lastTwo = implode(' ', array_slice($words, -2)); // 2 kata terakhir
                    } else {
                    // Jika hanya ada 2 kata atau kurang, tampilkan seluruhnya di dalam span
                    $before = '';
                    $lastTwo = $heroTitle;
                    }
                    @endphp

                    <h1 class="text-5xl lg:text-6xl leading-tight animate-slideInLeft pacifico-regular">
                        {{ $before }}
                        <span class="text-blue-600">{{ $lastTwo }}</span>
                    </h1>

                    <div class="flex gap-4 lg:text-xl mt-16">
                        <a href="https://wa.me/{{ $storeSetting->contact ?? '628123456789' }}" target="_blank"
                            class="px-8 py-4 border-2 border-gray-400 rounded-md hover:bg-gray-200 transition-all">
                            Pesan Sekarang
                        </a>
                        <a href="#menu" class="px-8 py-4 transition-all">
                            Pelajari Selengkapnya
                            <i class="bi bi-arrow-right ml-4"></i>
                        </a>
                    </div>
                </div>
                {{-- <div class="w-1/2 mt-12 lg:mt-0">
                    <div
                        class="overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-500 rounded-full">
                        @if (!empty($storeSetting->hero_image))
                        <img src="{{ asset('storage/' . $storeSetting->hero_image) }}" alt="Hero Image"
                            class="w-full h-full object-cover rounded-full">
                        @else
                        <img src="/assets/images/homepage/hero.jpeg" alt="Kue dan camilan Pawon3D"
                            class="w-full h-auto rounded-full object-cover">
                        @endif
                    </div>
                </div> --}}
            </div>
        </section>

        <!-- Featured Section -->
        <section class="pt-8 pb-8">
            <div class="mx-auto px-4">
                <h1 class="text-4xl pacifico-regular font-bold text-center space-y-4 mb-8">
                    Produk Unggulan Kami
                </h1>
                <p class="text-lg text-gray-600 m-auto text-center mb-8 lg:w-3/5">
                    Pilihan kue yang paling disukai pembeli! Dibuat dari bahan berkualitas dan rasa yang bikin nagih,
                    siap menemani hari-harimu.
                </p>

                @php
                $colCount = $products->count();
                @endphp
                <div
                    class="grid grid-cols-{{ $colCount > 1 ? 2 : 1 }} md:grid-cols-{{ $colCount > 4 ? 4 : $colCount }} gap-4 overflow-x-auto">
                    @forelse ($products as $product)
                    <div class="bg-transparent p-8">
                        <div class="flex justify-center mb-4">
                            @if($product->product_image)
                            <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                                class="w-40 h-40 rounded-md object-fill">
                            @else
                            <img src="{{ asset('/img/no-img.jpg')}}" alt="{{ $product->name }}"
                                class="w-40 h-40 rounded-md object-fill">
                            @endif
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg montserrat-regular font-semibold mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 mb-4 text-sm montserrat-regular">
                                @if ($product->reviews->count() > 0)
                                {{ number_format($product->reviews->avg('rating'), 1) }}
                                <i class="bi bi-star-fill text-yellow-500"></i>
                                @else
                                Belum ada penilaian
                                @endif
                                @if ($product->reviews->count() > 10)
                                ({{ $product->reviews->count() }}+ Penilai)
                                @else
                                ({{ $product->reviews->count() }} Penilai)
                                @endif
                            </p>
                            <p class="text-gray-600 mb-4 text-sm montserrat-regular">Rp {{
                                number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-600 mb-4">Tidak ada produk unggulan saat ini.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bg-blue-50 pt-8 pb-8">
            <div class="flex flex-col items-center justify-center text-center px-4">
                <h1 class="text-5xl lg:w-4xl pacifico-regular font-bold space-y-4 mb-8">
                    Beli Kue Favoritmu, Beri Ulasan dan Dapatkan Kode Hadiah!
                </h1>
                <p class="text-sm text-gray-500 m-auto montserrat-regular mb-8 lg:w-80">
                    Tukarkan untuk kue gratis di transaksi berikutnya.
                    Jangan sampai ketinggalan!
                </p>

                <a href="#" class="px-8 py-4 border-2 border-gray-400 rounded-md hover:bg-gray-200 transition-all">
                    Selengkapnya
                </a>
            </div>
        </section>

        <!-- Menu Section with Grid -->
        <section class="mx-auto px-4 py-4" id="menu">
            <div class="text-center mb-4">
                <h2 class="text-4xl font-poppins font-bold mb-4">Koleksi Kue & Camilan</h2>
            </div>
            <!-- Menu Items -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap justify-center -mb-px font-medium text-center" id="menu-tabs" role="tablist">
                    @foreach ($categories as $index => $category)
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-2 md:p-4 text-xs md:text-sm border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-colors
                    {{ $index === 0 ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent' }}"
                            id="tab-{{ $category->id }}" data-tabs-target="#panel-{{ $category->id }}" type="button"
                            role="tab" aria-controls="panel-{{ $category->id }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $category->name }}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div id="menu-content">
                @foreach ($categories as $index => $category)
                <div class="{{ $index === 0 ? 'block' : 'hidden' }}" id="panel-{{ $category->id }}" role="tabpanel"
                    aria-labelledby="tab-{{ $category->id }}">
                    @php
                    $products = \App\Models\Product::where('category_id', $category->id)->paginate(3);
                    @endphp
                    <div id="products-container"
                        class="grid grid-cols-3 gap-6 p-4 rounded-lg bg-white shadow-sm transition-all">
                        @forelse ($products as $product)
                        <div
                            class="group relative mb-6 overflow-hidden rounded-xl shadow-md hover:shadow-lg transition-shadow">
                            <div class="aspect-square overflow-hidden">
                                @if($product->product_image)
                                <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105">
                                @else
                                <img src="{{ asset('/img/no-img.jpg')}}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105">

                                @endif
                            </div>
                            <div class="p-4 bg-white">
                                <h3 class="text-xs md:text-lg font-semibold text-gray-800 mb-2">{{ $product->name }}
                                </h3>
                                <div class="flex justify-between items-center">
                                    <p class="text-blue-600 font-medium text-xs md:text-md">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center py-8 text-gray-500">
                            Tidak ada produk tersedia di kategori ini
                        </div>
                        @endforelse

                        <!-- Pagination -->
                        <div class="pagination col-span-full mt-4">
                            {{ $products->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Review Section Carousel -->
        {{-- <section class="container mx-auto px-4 py-16" id="reviews">
            <div class="text-center mb-8">
                <h2 class="text-4xl font-poppins font-bold mb-4">Apa Kata Mereka?</h2>
            </div>
            @if ($reviews->isEmpty())
            <div class="text-center text-gray-500">
                Belum ada ulasan untuk ditampilkan.
            </div>
            @elseif ($reviews->count() > 1)
            <div id="review-carousel" class="relative" data-carousel="slide">
                <!-- Carousel wrapper -->
                <div class="overflow-hidden relative h-64 rounded-lg">
                    @foreach($reviews as $index => $review)
                    <div class="hidden duration-700 ease-in-out" data-carousel-item {{ $index===0
                        ? 'data-carousel-active' : '' }}>
                        <div class="flex flex-col items-center justify-center h-full bg-white p-6 rounded-lg shadow-lg">
                            <h2 class="text-2xl font-semibold mb-4">{{ $review->product->name }}</h2>
                            @if ($review->product->product_image)
                            <img src="{{ asset('storage/'.$review->product->product_image) }}"
                                alt="{{ $review->product->name }}" class="w-24 h-24 rounded-md mb-4">
                            @endif
                            <h3 class="text-xl font-semibold mb-2">{{ $review->user_name }}</h3>
                            <div class="flex mb-4">
                                @for($i = 1; $i <= 5; $i++) <svg
                                    class="w-5 h-5 {{ $review->rating >= $i ? 'text-yellow-500' : 'text-gray-300' }}"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.39 2.46a1 1 0 00-.364 1.118l1.286 3.966c.3.921-.755 1.688-1.54 1.118l-3.39-2.46a1 1 0 00-1.176 0l-3.39 2.46c-.785.57-1.84-.197-1.54-1.118l1.286-3.966a1 1 0 00-.364-1.118L2.045 9.393c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.966z" />
                                    </svg>
                                    @endfor
                            </div>
                            <p class="text-gray-600 text-center">{{ $review->comment }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Slider indicators -->
                <div class="flex absolute bottom-5 left-1/2 z-30 space-x-3 -translate-x-1/2">
                    @foreach($reviews as $index => $review)
                    <button type="button"
                        class="w-3 h-3 rounded-full {{ $index === 0 ? 'bg-blue-600' : 'bg-gray-300' }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"
                        data-carousel-slide-to="{{ $index }}"></button>
                    @endforeach
                </div>

                <!-- Slider controls -->
                <button type="button"
                    class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-prev>
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50">
                        <svg aria-hidden="true" class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                </button>
                <button type="button"
                    class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                    data-carousel-next>
                    <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50">
                        <svg aria-hidden="true" class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                        <span class="sr-only">Next</span>
                    </span>
                </button>
            </div>

            @else
            <div class="overflow-hidden relative h-64 rounded-lg">
                @foreach($reviews as $index => $review)
                <div class="flex flex-col items-center justify-center h-full bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-semibold mb-4">{{ $review->product->name }}</h2>
                    @if ($review->product->product_image)
                    <img src="{{ asset('storage/'.$review->product->product_image) }}"
                        alt="{{ $review->product->name }}" class="w-24 h-24 rounded-md mb-4">
                    @endif
                    <h3 class="text-xl font-semibold mb-2">{{ $review->user_name }}</h3>
                    <div class="flex mb-4">
                        @for($i = 1; $i <= 5; $i++) <svg
                            class="w-5 h-5 {{ $review->rating >= $i ? 'text-yellow-500' : 'text-gray-300' }}"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.39 2.46a1 1 0 00-.364 1.118l1.286 3.966c.3.921-.755 1.688-1.54 1.118l-3.39-2.46a1 1 0 00-1.176 0l-3.39 2.46c-.785.57-1.84-.197-1.54-1.118l1.286-3.966a1 1 0 00-.364-1.118L2.045 9.393c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.966z" />
                            </svg>
                            @endfor
                    </div>
                    <p class="text-gray-600 text-center">{{ $review->comment }}</p>
                </div>

                @endforeach
            </div>
            @endif
        </section> --}}



        <!-- Contact Section -->
        <section class="bg-blue-50 py-20">
            <div class="mx-auto px-4">
                <div class="max-w-4xl mx-auto bg-transparent p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-4xl pacifico-regular font-bold mb-2">Wilayah dan Cara Pesan</h2>
                    </div>
                    <div class="flex flex-col md:flex-row gap-6">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!4v1742133851135!6m8!1m7!1sKwaGqQ5eD1Pjwm1wY_m6ng!2m2!1d-1.729048731821646!2d103.2726813742673!3f215.95130838744583!4f1.839142862723449!5f0.7820865974627469"
                            class="w-full md:w-1/2 h-[350px]" style="border: 0" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <div class="w-full md:w-1/2 flex flex-col items-center justify-center">
                            <a href="https://wa.me/{{ $storeSetting->contact ?? '628123456789' }}" target="_blank"
                                class="px-8 py-3 bg-green-500 text-white rounded-full hover:bg-green-600 transition-all flex items-center gap-2">
                                <i class="bi bi-whatsapp"></i>
                                Hubungi Kami
                            </a>
                            <p class="text-sm text-gray-600 mt-2 text-center">
                                Catatan: Kami hanya melayani pemesanan dan pengambilan langsung di toko. Tidak ada
                                layanan
                                antar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-12">
            <div class="container mx-auto px-4">
                <!-- Judul -->
                <h2 class="text-3xl pacifico-regular font-semibold text-center mb-8">Ulasan Produk</h2>

                <div class="flex flex-col lg:flex-row gap-8">
                    <div
                        class="flex-shrink-0 bg-white rounded-2xl shadow-md p-6 w-full lg:w-1/3 flex flex-col items-center">
                        <div
                            class="bg-gray-300 rounded-lg w-full h-64 mb-4 flex items-center justify-center text-gray-500">
                            Ilustrasi Ulasan
                        </div>
                        <a href="#"
                            class="mt-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                            Beri Ulasan
                        </a>
                    </div>

                    <!-- Carousel Ulasan Kecil -->
                    <div class="relative flex-1">
                        <div id="reviews" class="flex gap-6 overflow-x-auto no-scrollbar pb-2">
                            @foreach($productReviews as $product)
                            @php
                            $avgRating = number_format($product->reviews->avg('rating'), 1);
                            $latestReview = $product->reviews->sortByDesc('created_at')->first();
                            @endphp

                            <div class="min-w-[200px] bg-white rounded-2xl shadow p-4 flex-shrink-0">
                                {{-- Gambar produk --}}
                                @if($product->product_image)
                                <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                                    class="w-full h-32 object-cover rounded-md mb-3">
                                @else
                                <img src="{{ asset('img/no-img.jpg') }}" alt="{{ $product->name }}"
                                    class="w-full h-32 object-cover rounded-md mb-3">
                                @endif

                                {{-- Nama & Dimensi (jika ada) --}}
                                <h3 class="font-medium mb-1">
                                    {{ $product->name }}
                                </h3>

                                {{-- Rating --}}
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.966a1 1 0 00.95.69h4.18c.969
                             0 1.371 1.24.588 1.81l-3.388 2.462a1 1 0 00-.364 1.118l1.286 3.966c.3.921-.755
                             1.688-1.54 1.118l-3.388-2.462a1 1 0 00-1.175 0l-3.388 2.462c-.785.57-1.84-.197-1.54
                             -1.118l1.286-3.966a1 1 0 00-.364-1.118L2.045 9.393c-.783-.57-.38-1.81.588
                             -1.81h4.18a1 1 0 00.95-.69l1.286-3.966z" />
                                    </svg>
                                    <span class="text-gray-700 font-semibold">{{ $avgRating }}</span>
                                </div>

                                {{-- Cuplikan review --}}
                                <p class="text-gray-500 text-sm">
                                    {{ Str::limit($latestReview->comment ?? 'Belum ada komentar', 60) }}
                                </p>
                            </div>
                            @endforeach
                        </div>

                        <!-- Tombol Selengkapnya -->
                        <div class="mt-4 text-right">
                            <button id="loadMore"
                                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition">
                                Selengkapnya
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>


    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-10 px-5">
        <div
            class="max-w-6xl mx-auto text-center pb-8 mb-8 border-b-2 border-gray-100 flex flex-row items-center justify-between">
            <h1 class="text-4xl font-poppins font-bold mb-4">Pawon3D</h1>
            <h2 class="text-xl font-semibold mb-4">
                <a href="https://www.instagram.com/pawon3d/" target="_blank">
                    Ikuti Kami <i class="bi bi-instagram"></i>
                </a>
            </h2>
        </div>
        <div class="max-w-6xl mx-auto flex flex-wrap gap-8 justify-between">
            <!-- Kontak Kami -->
            <div class="min-w-[250px] flex-1">
                <h3 class="text-xl font-semibold mb-4 pb-2">Kontak Kami</h3>
                <ul class="space-y-2">
                    <li><a href="tel:081234567891"
                            class="text-gray-300 hover:text-blue-500 transition-colors">0812-3456-7891</a></li>
                    <li><a href="mailto:pawon3d@gmail.com"
                            class="text-gray-300 hover:text-blue-500 transition-colors">pawon3d@gmail.com</a></li>
                </ul>
            </div>

            <!-- Jelajahi -->
            <div class="min-w-[250px] flex-1">
                <h3 class="text-xl font-semibold mb-4 pb-2">Jelajahi</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Home</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Produk</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Cara Pernesanan</a>
                    </li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Ulasan</a></li>
                </ul>
            </div>

            <!-- Informasi -->
            <div class="min-w-[250px] flex-1">
                <h3 class="text-xl font-semibold mb-4 pb-2">Informasi</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Tentang Kami</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Kontak Kami</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Wilayah Pernesanan</a>
                    </li>
                </ul>
            </div>

            <!-- Bantuan -->
            <div class="min-w-[250px] flex-1">
                <h3 class="text-xl font-semibold mb-4 pb-2">Bantuan</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Cara Pernesanan</a>
                    </li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Metode Pembayaran</a>
                    </li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">FAQ</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Kebijakan Privasi</a>
                    </li>
                </ul>
            </div>
            <div class="min-w-[250px] flex-1">
                <h3 class="text-xl font-semibold mb-4 pb-2">Alamat dan Lokasi</h3>
                <div class="mb-4">
                    <p class="text-gray-300">Jl. Jenderal Sudirman Km.3<br>Jambi, Indonesia</p>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto mt-12 pt-8 border-gray-600">
            <div class="border-gray-700 mt-8 pt-8 text-center text-gray-400 py-4">
                <p>&copy; {{ date('Y') }} {{ $storeSetting->store_name ?? 'Pawon3D' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault(); // Mencegah perilaku default link
            var url = $(this).attr('href');
            

            $.ajax({
                url: url, 
                dataType: 'html', 
                success: function(data) {
                    // Cari konten baru di dalam response dan update container
                    var newContent = $(data).find('#products-container').html();
                    $('#products-container').html(newContent);
                    // Jika ingin mengubah URL tanpa reload halaman
                    history.pushState(null, '', url);
                }, 
                error: function() {
                    alert('Terjadi kesalahan saat mengambil data.');
                }
            });
        });

    </script>

    <!-- Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"
        integrity="sha512-H6cPm97FAsgIKmlBA4s774vqoN24V5gSQL4yBTDOY2su2DeXZVhQPxFK4P6GPdnZqM9fg1G3cMv5wD7e6cFLZQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        gsap.from(".animate-slideInLeft", {
            duration: 5
            , x: -100
            , opacity: 0
            , ease: "power4.out"
        });

        gsap.utils.toArray(".menu-item").forEach(item => {
            gsap.from(item, {
                scrollTrigger: {
                    trigger: item, 
                    start: "top center+=100"
                }, 
                opacity: 0, 
                y: 50, 
                duration: 0.8
            });
        });

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mendapatkan semua tombol tab
            const tabButtons = document.querySelectorAll('#menu-tabs button[role="tab"]');
            // Mendapatkan semua panel tab
            const tabPanels = document.querySelectorAll('#menu-content div[role="tabpanel"]');

            // Fungsi untuk mengaktifkan tab
            function activateTab(tabId) {
                // Menyembunyikan semua panel tab
                tabPanels.forEach(panel => {
                    panel.classList.add('hidden');
                    panel.classList.remove('block');
                });
                // Menonaktifkan semua tombol tab
                tabButtons.forEach(button => {
                    button.classList.remove('text-blue-600', 'border-blue-600');
                    button.classList.add('text-gray-500', 'border-transparent');
                    button.setAttribute('aria-selected', 'false');
                });
                // Mengaktifkan tab yang dipilih
                const selectedTab = document.getElementById(tabId);
                const targetPanelId = selectedTab.getAttribute('data-tabs-target');
                const targetPanel = document.querySelector(targetPanelId);
                if (selectedTab && targetPanel) {
                    selectedTab.classList.remove('text-gray-500', 'border-transparent');
                    selectedTab.classList.add('text-blue-600', 'border-blue-600');
                    selectedTab.setAttribute('aria-selected', 'true');
                    targetPanel.classList.remove('hidden');
                    targetPanel.classList.add('block');
                }
            }
            // Menambahkan event listener untuk setiap tombol tab
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    activateTab(this.id);
                });
            });
            // Mengaktifkan tab pertama secara default jika ada
            if (tabButtons.length > 0) {
                activateTab(tabButtons[0].id);
            }
        });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
      const btn  = document.getElementById('nav-toggle');
      const menu = document.getElementById('nav-menu');
  
      btn.addEventListener('click', () => {
        menu.classList.toggle('hidden');
      });
    });
    </script>

    <script>
        document.getElementById('loadMore').addEventListener('click', () => {
      const container = document.getElementById('reviews');
      container.scrollBy({ left: 300, behavior: 'smooth' });
    });
    </script>
</body>

</html>
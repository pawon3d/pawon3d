<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $storeProfile->name ?? 'Pawon3D' }}</title>
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
    @if (!empty($storeProfile->logo))
        <link rel="icon" href="{{ asset('storage/' . $storeProfile->logo) }}" type="image/x-icon" />
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
    <!-- Header -->
    <header class="sticky top-0 bg-[#74512d] z-50 shadow-sm">
        <nav class="max-w-[1280px] mx-auto px-[50px] py-0 h-[100px] flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-[40px]">
                <a href="/" class="w-[64px] h-[64px]" wire:navigate>
                    @if (!empty($storeProfile->logo))
                        <img src="{{ asset('storage/' . $storeProfile->logo) }}"
                            alt="{{ $storeProfile->name ?? 'Pawon3D' }}" class="w-full h-full object-contain">
                    @else
                        <img src="{{ asset('img/logo.png') }}" alt="{{ $storeProfile->name ?? 'Pawon3D' }}"
                            class="w-full h-full object-contain">
                    @endif
                </a>

                <!-- Navigation -->
                <div class="hidden md:flex items-center gap-[5px]">
                    <a href="/landing-produk"
                        class="px-[20px] py-[10px] text-[18px] montserrat-medium text-white hover:border-b-2 hover:border-white transition-all {{ request()->routeIs('landing-produk*') ? 'border-b-2 border-white' : '' }}">
                        Produk
                    </a>
                    <a href="#wilayah"
                        class="px-[20px] py-[10px] text-[18px] montserrat-medium text-white hover:border-b-2 hover:border-white transition-all">
                        Wilayah Pesan
                    </a>
                    <a href="#carapesan"
                        class="px-[20px] py-[10px] text-[18px] montserrat-medium text-white hover:border-b-2 hover:border-white transition-all">
                        Cara Pesan
                    </a>
                    <a href="#poin"
                        class="px-[20px] py-[10px] text-[18px] montserrat-medium text-white hover:border-b-2 hover:border-white transition-all">
                        Dapatkan Poin
                    </a>
                    <a href="#tentang"
                        class="px-[20px] py-[10px] text-[18px] montserrat-medium text-white hover:border-b-2 hover:border-white transition-all">
                        Tentang
                    </a>
                </div>
            </div>

            <!-- WhatsApp Button -->
            <a href="https://wa.me/{{ $storeProfile->contact ?? '628123456789' }}" target="_blank"
                class="hidden md:flex items-center gap-[10px] px-[20px] py-[10px] text-[20px] montserrat-medium text-white hover:bg-[#5d3f23] transition-all rounded-md">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                </svg>
                Pesan Sekarang
            </a>

            <!-- Mobile Menu Toggle -->
            <button id="nav-toggle" class="md:hidden text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>

        <!-- Mobile Menu -->
        <div id="nav-menu" class="hidden md:hidden bg-[#74512d] border-t border-[#5d3f23]">
            <div class="px-4 py-3 flex flex-col gap-2">
                <a href="/landing-produk" class="px-4 py-2 text-white montserrat-medium hover:bg-[#5d3f23] rounded">
                    Produk
                </a>
                <a href="#wilayah" class="px-4 py-2 text-white montserrat-medium hover:bg-[#5d3f23] rounded">
                    Wilayah Pesan
                </a>
                <a href="#carapesan" class="px-4 py-2 text-white montserrat-medium hover:bg-[#5d3f23] rounded">
                    Cara Pesan
                </a>
                <a href="#poin" class="px-4 py-2 text-white montserrat-medium hover:bg-[#5d3f23] rounded">
                    Dapatkan Poin
                </a>
                <a href="#tentang" class="px-4 py-2 text-white montserrat-medium hover:bg-[#5d3f23] rounded">
                    Tentang
                </a>
                <a href="https://wa.me/{{ $storeProfile->contact ?? '628123456789' }}" target="_blank"
                    class="px-4 py-2 text-white montserrat-medium bg-green-600 hover:bg-green-700 rounded flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    Pesan Sekarang
                </a>
            </div>
        </div>
    </header>


    <main class="">
        <!-- Hero Section with Animation -->
        <section class="px-4 py-20 mb-4 bg-blue-50">
            <div class="flex flex-col lg:flex-row items-center gap-8">
                <div class="lg:w-3/5 space-y-6">
                    <h3 class="text-xl font-semibold text-blue-600 animate-slideInLeft ml-2">
                        {{ $storeProfile->name ?? 'Pawon3D' }}
                    </h3>
                    @php
                        // Ambil hero title dari storeProfile, jika tidak ada gunakan default
                        $heroTitle = $storeProfile->tagline ?? 'Kue Rumahan Lezat, Sehangat Pelukan Ibu';

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
                        <a href="https://wa.me/{{ $storeProfile->contact ?? '628123456789' }}" target="_blank"
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
                        @if (!empty($storeProfile->hero_image))
                        <img src="{{ asset('storage/' . $storeProfile->hero_image) }}" alt="Hero Image"
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
                <div class="grid md:grid-cols-{{ $colCount > 4 ? 4 : $colCount }} gap-4 overflow-x-auto">
                    @forelse ($products as $product)
                        <div class="bg-transparent p-8">
                            <div class="flex justify-center mb-4">
                                @if ($product->product_image)
                                    <img src="{{ asset('storage/' . $product->product_image) }}"
                                        alt="{{ $product->name }}" class="w-40 h-40 rounded-md object-fill">
                                @else
                                    <img src="{{ asset('/img/no-img.jpg') }}" alt="{{ $product->name }}"
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
                                <p class="text-gray-600 mb-4 text-sm montserrat-regular">Rp
                                    {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 mb-4 text-center">Tidak ada produk unggulan saat ini.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bg-blue-50 pt-8 pb-8">
            <div class="flex flex-col items-center justify-center text-center px-4">
                <h1 class="text-5xl lg:w-4xl pacifico-regular font-bold space-y-4 mb-8">
                    Jadi Pelanggan dan Kumpulkan Poin!
                </h1>
                <p class="text-sm text-gray-500 m-auto montserrat-regular mb-8 lg:w-80">
                    Dapatkan
                    <span class="font-semibold">1 Poin</span>
                    dengan belanja kelipatan
                    <span class="font-semibold">Rp10.000.</span>
                    Poin juga dapat dikumpulkan melalui review di
                    <span class="font-semibold">story media sosial</span>
                    dan review toko di
                    <span class="font-semibold">Google Maps.</span>
                </p>

                <a href="#"
                    class="px-8 py-4 border-2 border-gray-400 rounded-md hover:bg-gray-200 transition-all">
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
                <ul class="flex flex-wrap justify-center -mb-px font-medium text-center" id="menu-tabs"
                    role="tablist">
                    @foreach ($categories as $index => $category)
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-2 md:p-4 text-xs md:text-sm border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-colors
                    {{ $index === 0 ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent' }}"
                                id="tab-{{ $category->id }}" data-tabs-target="#panel-{{ $category->id }}"
                                type="button" role="tab" aria-controls="panel-{{ $category->id }}"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                {{ $category->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div id="menu-content">
                @foreach ($categories as $index => $category)
                    <div class="{{ $index === 0 ? 'block' : 'hidden' }}" id="panel-{{ $category->id }}"
                        role="tabpanel" aria-labelledby="tab-{{ $category->id }}">
                        @php
                            $products = \App\Models\Product::latest()->paginate(3);
                        @endphp
                        <div id="products-container"
                            class="grid grid-cols-3 gap-6 p-4 rounded-lg bg-white shadow-sm transition-all">
                            @forelse ($products as $product)
                                <div
                                    class="group relative mb-6 overflow-hidden rounded-xl shadow-md hover:shadow-lg transition-shadow">
                                    <div class="aspect-square overflow-hidden">
                                        @if ($product->product_image)
                                            <img src="{{ asset('storage/' . $product->product_image) }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <img src="{{ asset('/img/no-img.jpg') }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105">
                                        @endif
                                    </div>
                                    <div class="p-4 bg-white">
                                        <h3 class="text-xs md:text-lg font-semibold text-gray-800 mb-2">
                                            {{ $product->name }}
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
                            <a href="https://wa.me/{{ $storeProfile->contact ?? '628123456789' }}" target="_blank"
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

        {{-- <section class="py-12">
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
                            @foreach ($productReviews as $product)
                            @php
                            $avgRating = number_format($product->reviews->avg('rating'), 1);
                            $latestReview = $product->reviews->sortByDesc('created_at')->first();
                            @endphp

                            <div class="min-w-[200px] bg-white rounded-2xl shadow p-4 flex-shrink-0">

                                @if ($product->product_image)
                                <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}"
                                    class="w-full h-32 object-cover rounded-md mb-3">
                                @else
                                <img src="{{ asset('img/no-img.jpg') }}" alt="{{ $product->name }}"
                                    class="w-full h-32 object-cover rounded-md mb-3">
                                @endif

                                <h3 class="font-medium mb-1">
                                    {{ $product->name }}
                                </h3>

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
        </section> --}}

    </main>


    <!-- Footer -->
    <footer class="bg-[#252324] text-white py-10">
        <div class="max-w-[1280px] mx-auto px-4">
            <!-- Logo and Social Media -->
            <div class="flex items-center justify-between pb-8 mb-8 border-b-2 border-white">
                <div class="flex items-center gap-4">
                    <div class="w-[100px] h-[100px]">
                        @if (!empty($storeProfile->logo))
                            <img src="{{ asset('storage/' . $storeProfile->logo) }}"
                                alt="{{ $storeProfile->name ?? 'Pawon3D' }}" class="w-full h-full object-contain">
                        @else
                            <img src="{{ asset('img/logo.png') }}" alt="{{ $storeProfile->name ?? 'Pawon3D' }}"
                                class="w-full h-full object-contain">
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <h3 class="text-[20px] montserrat-bold">Ikuti Kami</h3>
                    @if ($storeProfile->social_instagram)
                        <a href="{{ $storeProfile->social_instagram }}" target="_blank"
                            class="w-[40px] h-[40px] flex items-center justify-center hover:bg-[#74512d] rounded-full transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    @endif
                    @if ($storeProfile->social_facebook)
                        <a href="{{ $storeProfile->social_facebook }}" target="_blank"
                            class="w-[40px] h-[40px] flex items-center justify-center hover:bg-[#74512d] rounded-full transition-all">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    @endif
                    <div class="w-[68px] h-[68px]">
                        <img src="{{ asset('img/halal.png') }}" alt="Halal" class="w-full h-full object-contain">
                    </div>
                </div>
            </div>

            <!-- Footer Content Grid -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8 mb-8">
                <!-- Kontak Kami -->
                <div>
                    <h3 class="text-[20px] montserrat-bold mb-4">Kontak Kami</h3>
                    <div class="flex flex-col gap-4">
                        <a href="tel:{{ $storeProfile->contact ?? '081234567891' }}"
                            class="flex items-center gap-2 text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 00-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.01 21c.71 0 .99-.63.99-1.18v-3.45c0-.54-.45-.99-.99-.99z" />
                            </svg>
                            {{ $storeProfile->contact ?? '0812-3456-7891' }}
                        </a>
                        <a href="mailto:{{ $storeProfile->email ?? 'pawon3d@gmail.com' }}"
                            class="flex items-center gap-2 text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                            </svg>
                            {{ $storeProfile->email ?? 'pawon3d@gmail.com' }}
                        </a>
                    </div>
                </div>

                <!-- Jelajahi -->
                <div>
                    <h3 class="text-[20px] montserrat-bold mb-4">Jelajahi</h3>
                    <ul class="flex flex-col gap-4">
                        <li><a href="/landing-produk"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">Produk</a>
                        </li>
                        <li><a href="#wilayah"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">Wilayah
                                Pesan</a></li>
                        <li><a href="#carapesan"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">Cara
                                Pesan</a></li>
                        <li><a href="#poin"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">Dapatkan
                                Poin</a></li>
                    </ul>
                </div>

                <!-- Informasi -->
                <div>
                    <h3 class="text-[20px] montserrat-bold mb-4">Informasi</h3>
                    <ul class="flex flex-col gap-4">
                        <li><a href="#tentang"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">Tentang
                                Kami</a></li>
                        <li><a href="#wilayah"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">Lokasi
                                Kami</a></li>
                        <li><a href="#carapesan"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">Kontak
                                Kami</a></li>
                    </ul>
                </div>

                <!-- Bantuan -->
                <div>
                    <h3 class="text-[20px] montserrat-bold mb-4">Bantuan</h3>
                    <ul class="flex flex-col gap-4">
                        <li><a href="#"
                                class="text-[#c4c4c4] montserrat-regular text-[18px] hover:text-white transition-colors">FAQ</a>
                        </li>
                    </ul>
                </div>

                <!-- Alamat dan Lokasi -->
                <div>
                    <h3 class="text-[20px] montserrat-bold mb-4">Alamat dan Lokasi</h3>
                    <div class="flex flex-col gap-2">
                        @if ($storeProfile->address)
                            <p class="text-[#c4c4c4] montserrat-regular text-[18px]">{{ $storeProfile->address }}</p>
                        @else
                            <p class="text-[#c4c4c4] montserrat-regular text-[18px]">Jl. Jenderal Sudirman Km.3</p>
                            <p class="text-[#c4c4c4] montserrat-regular text-[18px]">Jambi, Indonesia</p>
                        @endif
                        @if ($storeProfile->location)
                            <a href="{{ $storeProfile->location }}" target="_blank"
                                class="text-[#74512d] montserrat-medium text-[16px] hover:underline mt-2">
                                Lihat di Peta →
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center pt-8">
                <p class="text-[20px] montserrat-medium text-white">
                    © {{ date('Y') }} {{ $storeProfile->name ?? 'Pawon3D' }}. All rights reserved
                </p>
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
            duration: 5,
            x: -100,
            opacity: 0,
            ease: "power4.out"
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
            const btn = document.getElementById('nav-toggle');
            const menu = document.getElementById('nav-menu');

            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        });
    </script>

    <script>
        document.getElementById('loadMore').addEventListener('click', () => {
            const container = document.getElementById('reviews');
            container.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>

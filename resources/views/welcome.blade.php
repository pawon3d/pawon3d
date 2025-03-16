<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pawon3D</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|poppins:400,500,600,700" rel="stylesheet" />
    <link href="{{ asset('flowbite/flowbite.min.css') }}" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" />
    <style>
        html {
            scroll-behavior: smooth;
        }

    </style>
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] font-instrument-sans">
    <!-- Enhanced Header -->
    <header class="sticky top-0 bg-white backdrop-blur-sm z-50 shadow-sm">
        <nav class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="/" class="text-3xl font-bold text-blue-600 hover:text-blue-700 transition-colors">
                <span class="text-blue-400">Pawon</span>3D
            </a>
            <div class="flex items-center gap-4">
                @auth
                <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors text-sm">
                    Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="px-5 py-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors text-sm">
                    Login
                </a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- Hero Section with Animation -->
    <section class="container mx-auto px-4 py-20 mt-5">
        <div class="flex flex-col lg:flex-row items-center gap-8">
            <div class="w-3/5 space-y-6">
                <h1 class="text-5xl lg:text-6xl font-poppins font-bold leading-tight animate-slideInLeft">
                    Savor the Art of
                    <span class="text-blue-600">Healthy Dining</span>
                </h1>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Experience nutrition-packed meals crafted with locally-sourced ingredients and chef's expertise.
                </p>
                <div class="flex gap-4">
                    <a href="#menu" class="px-8 py-3 border-2 border-blue-600 text-blue-600 rounded-full hover:bg-blue-50 transition-all">
                        Explore Menu
                    </a>
                </div>
            </div>
            <div class="w-1/2 mt-12 lg:mt-0">
                <div class="overflow-hidden shadow-xl transform hover:scale-105 transition-transform duration-500 rounded-full">
                    <img src="/assets/images/homepage/hero.jpeg" alt="Healthy Food" class="w-full h-auto rounded-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Section -->
    <section class="bg-blue-50 py-16">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg transition-transform hover:scale-105">
                    <div class="text-blue-600 text-4xl mb-4">
                        <i class="bi bi-star"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Premium Quality</h3>
                    <p class="text-gray-600">Locally-sourced organic ingredients</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg transition-transform hover:scale-105">
                    <div class="text-blue-600 text-4xl mb-4">
                        <i class="bi bi-bag"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Large Quantity Orders</h3>
                    <p class="text-gray-600">We can provide you with large quantity orders</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg transition-transform hover:scale-105">
                    <div class="text-blue-600 text-4xl mb-4">
                        <i class="bi bi-heart"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Healthy Options</h3>
                    <p class="text-gray-600">Nutritionist-approved meals</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section with Grid -->
    <section class="container mx-auto px-4 py-4" id="menu">
        <div class="text-center mb-4">
            <h2 class="text-4xl font-poppins font-bold mb-4">Menu</h2>
        </div>
        <!-- Menu Items -->
        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap justify-center -mb-px text-sm font-medium text-center" id="menu-tabs" role="tablist">
                @foreach ($categories as $index => $category)
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 transition-colors
                            {{ $index === 0 ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent' }}" id="tab-{{ $category->id }}" data-tabs-target="#panel-{{ $category->id }}" type="button" role="tab" aria-controls="panel-{{ $category->id }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        {{ $category->name }}
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <div id="menu-content">
            @foreach ($categories as $index => $category)
            <div class="{{ $index === 0 ? 'block' : 'hidden' }} grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-4 rounded-lg bg-white shadow-sm transition-all" id="panel-{{ $category->id }}" role="tabpanel" aria-labelledby="tab-{{ $category->id }}">

                @forelse ($category->products as $product)
                <div class="group relative mb-6 overflow-hidden rounded-xl shadow-md hover:shadow-lg transition-shadow">
                    <div class="aspect-square overflow-hidden">
                        @if($product->product_image)
                        <img src="{{ asset('storage/'.$product->product_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105">
                        @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <span class="text-gray-400">No image available</span>
                        </div>
                        @endif
                    </div>

                    <div class="p-4 bg-white">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $product->name }}</h3>
                        <div class="flex justify-between items-center">
                            <p class="text-blue-600 font-medium text-lg">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    No products available in this category
                </div>
                @endforelse
            </div>
            @endforeach
        </div>
    </section>

    <!-- contact Section -->
    <section class="bg-gray-100 py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <h2 class="text-4xl font-poppins font-bold mb-2">Pesan Sekarang</h2>
                </div>

                <div class="flex justify-between">
                    <iframe src="https://www.google.com/maps/embed?pb=!4v1742133851135!6m8!1m7!1sKwaGqQ5eD1Pjwm1wY_m6ng!2m2!1d-1.729048731821646!2d103.2726813742673!3f215.95130838744583!4f1.839142862723449!5f0.7820865974627469" class="h-[350px] w-1/2 max-md:w-full" style="border: 0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="w-1/2 flex flex-col mx-4 items-center justify-center">
                        <a href="https://wa.me/6281234567890" target="_blank" class="px-8 py-3 bg-green-500 text-white rounded-full hover:bg-green-600 transition-all flex items-center gap-2">
                            <i class="bi bi-whatsapp"></i>
                            Pawon3D
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 py-4">
            <p>&copy; {{ date('Y') }} Pawon3D. All rights reserved.</p>
        </div>
        </div>
    </footer>


    <!-- Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js" integrity="sha512-H6cPm97FAsgIKmlBA4s774vqoN24V5gSQL4yBTDOY2su2DeXZVhQPxFK4P6GPdnZqM9fg1G3cMv5wD7e6cFLZQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                    trigger: item
                    , start: "top center+=100"
                }
                , opacity: 0
                , y: 50
                , duration: 0.8
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

</body>
</html>

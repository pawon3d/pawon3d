<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
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

        #nav-menu {
            transform-origin: top center;
            transition:
                transform 0.3s ease-out,
                opacity 0.2s ease-out,
                visibility 0.3s ease-out;
        }

        #nav-menu.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
            visibility: visible;
        }
    </style>
</head>

<body class="bg-[#FDFDFC] text-[#1b1b18] montserrat-regular">
    <!-- Enhanced Header -->
    <header class="sticky top-0 bg-white backdrop-blur-sm z-50 shadow-sm">
        <nav class="container mx-auto px-4 py-3 flex items-center">
            <a href="/" class="lg:text-3xl text-lg font-bold text-blue-600 hover:text-blue-700 transition-colors">
                <span class="text-blue-400">{{ $storeProfile->name ?? 'Pawon3D' }}</span>
            </a>

            <flux:navbar class="hidden md:flex ml-4 flex-row gap-6">
                <flux:navbar.item href="/" :current="request()->routeIs('home')">Home</flux:navbar.item>
                <flux:navbar.item href="/landing-produk" :current="request()->routeIs('landing-produk*')">Produk
                </flux:navbar.item>
                <flux:navbar.item href="/landing-cara-pesan" :current="request()->routeIs('landing-cara-pesan')">Cara
                    Pesan</flux:navbar.item>
            </flux:navbar>
            <div class="hidden md:flex ml-auto">
                @auth
                <a href="{{ route('ringkasan-umum') }}"
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
            class="absolute top-full left-0 w-full bg-white flex-col gap-4 px-4 py-3 shadow-md md:hidden transform -translate-y-4 opacity-0 transition-all duration-300 ease-out pointer-events-none">
            <flux:navbar.item href="/" :current="request()->routeIs('home')">Home</flux:navbar.item>
            <flux:navbar.item href="/landing-produk" :current="request()->routeIs('landing-produk*')">Produk
            </flux:navbar.item>
            <flux:navbar.item href="/landing-cara-pesan" :current="request()->routeIs('landing-cara-pesan')">Cara Pesan
            </flux:navbar.item>

            {{-- tombol auth --}}
            @auth
            <a href="{{ route('ringkasan-umum') }}"
                class="block px-4 py-2 bg-blue-600 text-white rounded-full text-center">
                Dashboard
            </a>
            @else
            <a href="{{ route('login') }}" class="block px-4 py-2 bg-blue-600 text-white rounded-full text-center">
                Login
            </a>
            @endauth
        </flux:navbar>
    </header>

    <main>
        {{ $slot }}
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
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Cara Pesan</a>
                    </li>
                </ul>
            </div>

            <!-- Informasi -->
            <div class="min-w-[250px] flex-1">
                <h3 class="text-xl font-semibold mb-4 pb-2">Informasi</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Tentang Kami</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Kontak Kami</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Wilayah Pemesanan</a>
                    </li>
                </ul>
            </div>

            <!-- Bantuan -->
            <div class="min-w-[250px] flex-1">
                <h3 class="text-xl font-semibold mb-4 pb-2">Bantuan</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Cara Pemesanan</a>
                    </li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">Metode Pembayaran</a>
                    </li>
                    <li><a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">FAQ</a></li>

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
                <p>&copy; {{ date('Y') }} {{ $storeProfile->name ?? 'Pawon3D' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <!-- Animations -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const btn  = document.getElementById('nav-toggle');
        const menu = document.getElementById('nav-menu');
        
        btn.addEventListener('click', () => {
            menu.classList.toggle('open');
            
            // Tambahkan overflow hidden ke body saat menu terbuka
            if(menu.classList.contains('open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        });
    </script>
</body>

</html>
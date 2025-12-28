<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="{{ $metaDescription ?? ($storeProfile->description ?? 'Pawon3D - Kue Rumahan Lezat, Sehangat Pelukan Ibu') }}" />
<meta name="keywords" content="kue, bakery, jambi, pawon3d, kue tradisional, snack box, catering jambi" />
<meta name="author" content="{{ $storeProfile->name ?? 'Pawon3D' }}" />
<meta name="robots" content="index, follow" />

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:title" content="{{ $title ?? ($storeProfile->name ?? 'Pawon3D') }}" />
<meta property="og:description" content="{{ $metaDescription ?? ($storeProfile->description ?? 'Pawon3D - Kue Rumahan Lezat, Sehangat Pelukan Ibu') }}" />
<meta property="og:image" content="{{ !empty($storeProfile->logo) ? asset('storage/' . $storeProfile->logo) : asset('assets/bakery-logo.png') }}" />

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image" />
<meta property="twitter:url" content="{{ url()->current() }}" />
<meta property="twitter:title" content="{{ $title ?? ($storeProfile->name ?? 'Pawon3D') }}" />
<meta property="twitter:description" content="{{ $metaDescription ?? ($storeProfile->description ?? 'Pawon3D - Kue Rumahan Lezat, Sehangat Pelukan Ibu') }}" />
<meta property="twitter:image" content="{{ !empty($storeProfile->logo) ? asset('storage/' . $storeProfile->logo) : asset('assets/bakery-logo.png') }}" />

<link rel="canonical" href="{{ url()->current() }}" />
@stack('meta')

<title>{{ $title ?? 'Pawon3D' }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
@if (!empty($storeProfile->logo))
    <link rel="icon" href="{{ asset('storage/' . $storeProfile->logo) }}" type="image/x-icon" />
@endif
<link rel="stylesheet" type="text/css" href="{{ asset('css/pikaday.css') }}" />
<link href="{{ asset('flowbite/flowbite.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('flowbite/flatpickr.min.css') }}">
<link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('flowbite/flatpickr.js') }}"></script>
<script src="{{ asset('scripts/jquery.min.js') }}"></script>
<script src="{{ asset('scripts/select2.min.js') }}"></script>
@yield('css')
@livewireStyles
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- PWA: Load on all pages for mobile compatibility --}}
@laravelPWA

{{-- Livewire request timeout configuration --}}
<script>
    document.addEventListener('livewire:init', () => {
        // Tingkatkan timeout untuk request yang berat (60 detik)
        Livewire.hook('request', ({
            fail
        }) => {
            // Handle request timeout gracefully
        });

        // Handle failed requests - refresh halaman jika timeout
        Livewire.hook('request', ({
            respond,
            fail
        }) => {
            respond((response) => {
                // Request berhasil
            });

            fail(({
                status,
                content,
                preventDefault
            }) => {
                if (status === 419) {
                    // Session expired
                    window.location.reload();
                    preventDefault();
                }
            });
        });
    });
</script>

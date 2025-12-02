<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? 'Laravel' }}</title>

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

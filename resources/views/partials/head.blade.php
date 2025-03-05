<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? 'Laravel' }}</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<link href="{{ asset('flowbite/flowbite.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('flowbite/flatpickr.min.css') }}">
<script src="{{ asset('flowbite/flatpickr.js') }}"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

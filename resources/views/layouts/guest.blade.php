<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema Agua') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased overflow-x-hidden">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 px-3 sm:px-6">
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center group focus:outline-none">
                <svg class="w-14 h-16 sm:w-16 sm:h-20 drop-shadow-md transition-transform duration-200 group-hover:-translate-y-0.5"
                    viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <defs>
                        <linearGradient id="gotaAguaGuestLayout" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#5EEAD4" />
                            <stop offset="50%" stop-color="#0F766E" />
                            <stop offset="100%" stop-color="#115E59" />
                        </linearGradient>
                    </defs>

                    <path d="M50 5 C50 5 10 55 10 78
                            C10 101 27 115 50 115
                            C73 115 90 101 90 78
                            C90 55 50 5 50 5Z" fill="url(#gotaAguaGuestLayout)" />

                    <path d="M32 75 C32 62 43 43 48 36" fill="none" stroke="white" stroke-width="5"
                        stroke-linecap="round" opacity="0.7" />

                    <ellipse cx="35" cy="85" rx="5" ry="8" fill="white" opacity="0.35" />
                </svg>
            </a>
        </div>

        <div
            class="w-full sm:max-w-md mt-4 px-4 sm:px-6 py-5 sm:py-6 bg-white border border-slate-200 shadow-sm overflow-hidden rounded-xl sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>
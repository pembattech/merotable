<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('storage/logo/merotable-LOGO.webp') }}" type="image/x-icon">
    {{-- <img class="logo h-8 w-8 object-contain" src="{{ asset('storage/logo/merotable-LOGO.webp') }}" alt="Logo"> --}}

    @include('components.pwa')

    <title>@yield('title', 'Super Admin') — MeroTable</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
        }

        /* hidden until auth guard confirms a valid session, avoids content flash */
        body {
            visibility: hidden;
        }


        /* scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #a9a9a9;
            border-radius: 5px;
        }

        /* field input */
        .field-input {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.875rem;
            color: #1f2937;
            background: #f9fafb;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        .field-input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .field-input::placeholder {
            color: #9ca3af;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .animate-slide-up {
            animation: slideUp 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .d1 {
            animation-delay: .04s
        }

        .d2 {
            animation-delay: .08s
        }

        .d3 {
            animation-delay: .12s
        }

        .d4 {
            animation-delay: .16s
        }

        .d5 {
            animation-delay: .20s
        }

        .d6 {
            animation-delay: .24s
        }
    </style>

    {{-- Assets (Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="bg-gray-100">

    @include('super-admin.partials.auth-guard')

    @include('super-admin.partials.sidebar')

    <main class="ml-72 p-8">
        @yield('content')
    </main>

</body>

</html>
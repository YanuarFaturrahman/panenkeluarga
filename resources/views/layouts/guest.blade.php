<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'PanenKeluarga') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@600;700&display=swap" rel="stylesheet" />

    <!-- CDN Tailwind CSS + Custom Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pk-dark': '#214332',
                        'pk-cream': '#f1f3e9',
                        'pk-orange': '#e07a5f',
                        'pk-green': '#538253',
                    }
                }
            }
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans text-gray-900 antialiased bg-pk-cream min-h-screen">
    
    <!-- PENTING: Dibuat w-full tanpa pembatas max-w-md agar komponen form bisa mengontrol lebarnya sendiri -->
    <main class="w-full min-h-screen">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
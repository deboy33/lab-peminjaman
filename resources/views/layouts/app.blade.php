<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Peminjaman Lab') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <nav class="bg-slate-900 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="font-bold">Peminjaman Laboratorium</a>
            <div class="flex items-center gap-4 text-sm">
                @auth
                    <span>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="underline">Keluar</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>

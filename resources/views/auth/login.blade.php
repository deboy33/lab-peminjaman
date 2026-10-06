<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Peminjaman Laboratorium</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-white">Peminjaman Laboratorium</h1>
            <p class="text-indigo-200 mt-1">Silakan login untuk melanjutkan</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-rose-50 ring-1 ring-rose-200 text-rose-800 px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg ring-1 ring-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="w-full rounded-lg ring-1 ring-slate-200 px-3 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded">
                    Ingat saya
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 transition">
                    Login
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-1">
                <p class="font-semibold text-slate-600 mb-1">Akun Demo (password: <code class="bg-slate-100 px-1 rounded">password</code>)</p>
                <p>Mahasiswa: <code class="bg-slate-100 px-1 rounded">mahasiswa@lab.test</code></p>
                <p>Dosen: <code class="bg-slate-100 px-1 rounded">dosen@lab.test</code></p>
                <p>Laboran: <code class="bg-slate-100 px-1 rounded">laboran@lab.test</code></p>
                <p>Kalab: <code class="bg-slate-100 px-1 rounded">kalab@lab.test</code></p>
            </div>
        </div>
    </div>
</body>
</html>

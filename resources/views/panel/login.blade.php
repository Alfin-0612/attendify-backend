<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Attendify Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#6254F5', soft: '#F0EEFF' } } } } };
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#7868FF] via-brand to-[#5143E8] flex items-center justify-center p-5">
<div class="w-full max-w-sm bg-white rounded-3xl shadow-2xl p-8">
    <div class="w-14 h-14 rounded-2xl bg-brand text-white font-bold text-3xl flex items-center justify-center mx-auto">A</div>
    <h1 class="text-2xl font-bold text-center mt-4">Panel Attendify</h1>
    <p class="text-sm text-slate-500 text-center mb-6">Untuk guru, wali kelas, dan admin sekolah</p>

    <form method="POST" action="{{ route('panel.login.proses') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1" for="username">Username</label>
            <input id="username" name="username" value="{{ old('username') }}" autofocus required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password">Password</label>
            <input id="password" type="password" name="password" required
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="ingat" value="1" class="rounded"> Ingat saya
        </label>

        @if ($errors->any())
            <p class="text-sm text-red-600">{{ $errors->first() }}</p>
        @endif

        <button class="w-full bg-brand text-white rounded-lg py-2.5 font-semibold hover:opacity-90">Masuk</button>
    </form>
</div>
</body>
</html>

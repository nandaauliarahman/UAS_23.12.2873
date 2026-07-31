<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Organizer - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
        <h1 class="text-3xl font-black mb-2">Login Organizer</h1>
        <p class="text-slate-500 mb-8">Masuk untuk mengelola event milik kepanitiaanmu.</p>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('organizer.login.post') }}" class="space-y-5">
            @csrf
            <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email"
                class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
            <input name="password" type="password" required placeholder="Password"
                class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
            <button class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black hover:bg-indigo-700">Masuk</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Belum punya akun?
            <a href="{{ route('organizer.register') }}" class="text-indigo-600 font-bold">Daftar organizer</a>
        </p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Organizer - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-6 py-10">
    <div class="w-full max-w-2xl bg-white border border-slate-200 rounded-3xl p-8 shadow-sm">
        <h1 class="text-3xl font-black mb-2">Daftar Penyelenggara</h1>
        <p class="text-slate-500 mb-8">Akun perlu disetujui superadmin sebelum bisa publish event.</p>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('organizer.register.post') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <input name="tenant_name" value="{{ old('tenant_name') }}" required placeholder="Nama Kepanitiaan/HIMA"
                    class="px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
                <input name="pic_name" value="{{ old('pic_name') }}" required placeholder="Nama PIC"
                    class="px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
            </div>
            <textarea name="description" rows="3" placeholder="Deskripsi singkat penyelenggara"
                class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">{{ old('description') }}</textarea>
            <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email login"
                class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <input name="password" type="password" required placeholder="Password"
                    class="px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
                <input name="password_confirmation" type="password" required placeholder="Konfirmasi password"
                    class="px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
            </div>
            <button class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black hover:bg-indigo-700">Daftar</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Sudah punya akun?
            <a href="{{ route('organizer.login') }}" class="text-indigo-600 font-bold">Login</a>
        </p>
    </div>
</body>
</html>

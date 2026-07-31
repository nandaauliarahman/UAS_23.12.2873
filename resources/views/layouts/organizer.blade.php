<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Organizer') - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('organizer.dashboard') }}" class="flex items-center gap-3 font-black text-xl">
                <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center">OH</span>
                Organizer Hub
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('organizer.dashboard') }}" class="px-4 py-2 rounded-xl font-bold {{ request()->routeIs('organizer.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                <a href="{{ route('organizer.events.index') }}" class="px-4 py-2 rounded-xl font-bold {{ request()->routeIs('organizer.events.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">Event</a>
                <a href="{{ route('organizer.check-in.index') }}" class="px-4 py-2 rounded-xl font-bold {{ request()->routeIs('organizer.check-in.*') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-100' }}">Check-in</a>
                <form action="{{ route('organizer.logout') }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 rounded-xl font-bold text-rose-600 hover:bg-rose-50">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10">
        <header class="mb-8">
            <h1 class="text-3xl font-black">@yield('page_title', 'Dashboard')</h1>
            <p class="text-slate-500 font-medium">@yield('page_subtitle')</p>
        </header>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl font-bold">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

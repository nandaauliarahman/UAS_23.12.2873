@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="text-center space-y-6">
        <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">#1 Event Platform</span>
        <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
            Temukan & Pesan <span class="text-indigo-600">Tiket Event</span> Impianmu.
        </h1>
        <p class="text-lg text-slate-500 max-w-lg mx-auto leading-relaxed">
            Dari konser musik hingga workshop teknologi, semua ada di genggamanmu.
        </p>
    </div>
</section>

<!-- Filter Kategori -->
<section class="max-w-7xl mx-auto px-6 mb-8">
    <div class="mb-8 flex gap-3 justify-center flex-wrap">
        <a href="/"
            class="px-5 py-2 {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} rounded-xl font-bold transition">
            Semua Kategori
        </a>
        @foreach($categories as $cat)
            <a href="/?category={{ $cat->slug }}"
                class="px-5 py-2 {{ request('category') == $cat->slug ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }} rounded-xl font-bold transition">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>
</section>

<!-- Events Grid -->
<section class="max-w-7xl mx-auto px-6 pb-20">

    @if(request('category'))
        <p class="text-slate-500 mb-6 font-medium">
            Filter aktif: <span class="font-bold text-indigo-600">{{ request('category') }}</span>
            — <a href="/" class="underline hover:text-indigo-600">Reset</a>
        </p>
    @endif

    @if($events->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($events as $event)
        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="relative overflow-hidden aspect-[3/4]">
                <img src="{{ ($event->poster_path && Storage::disk('public')->exists($event->poster_path))
                    ? asset('storage/' . $event->poster_path)
                    : 'https://placehold.co/200x600' }}" alt="{{ $event->title }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur rounded-lg text-xs font-bold uppercase text-indigo-600">
                    {{ $event->category->name }}
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2 group-hover:text-indigo-600 transition">{{ $event->title }}</h3>
                <div class="flex items-center gap-2 text-slate-500 text-sm mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::parse($event->date)->format('d-m-Y H:i') }}</span>
                </div>
                <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <span>{{ $event->location }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t">
                    <span class="text-2xl font-black text-indigo-600">Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                    <a href="{{ route('events.show', $event->id) }}"
                        class="px-5 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-20">
        <p class="text-slate-400 text-xl font-bold">Tidak ada event ditemukan.</p>
        <a href="/" class="mt-4 inline-block text-indigo-600 font-bold hover:underline">Lihat semua event</a>
    </div>
    @endif

</section>

@endsection
@extends('layouts.app')

@section('content')
<main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
    <div class="lg:col-span-1">
        <div class="sticky top-32 space-y-8">
            <img src="{{ ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                ? asset('storage/' . $event->poster_path)
                : 'https://placehold.co/600x800' }}" alt="{{ $event->title }}"
                class="w-full rounded-3xl shadow-2xl border-8 border-white object-cover aspect-[3/4]">

            <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="font-bold mb-4">Penyelenggara</h4>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                        {{ strtoupper(substr($event->tenant->name ?? 'AH', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $event->tenant->name ?? 'AmikomEventHub' }}</p>
                        <p class="text-xs text-slate-500">{{ $event->tenant?->is_approved ? 'Verified Organizer' : 'Official Event' }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                <h4 class="font-bold mb-3">Rating Event</h4>
                @if($reviewCount > 0)
                    <div class="flex items-center gap-2">
                        <div class="flex text-amber-400">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'fill-current' : 'fill-slate-200' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.363 1.118l1.287 3.958c.299.921-.755 1.688-1.538 1.118l-3.367-2.448a1 1 0 00-1.176 0l-3.367 2.448c-.783.57-1.837-.197-1.538-1.118l1.287-3.958a1 1 0 00-.363-1.118L2.062 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="font-bold text-slate-700">{{ $averageRating }}</span>
                        <span class="text-slate-400 text-sm">({{ $reviewCount }} ulasan)</span>
                    </div>
                @else
                    <p class="text-slate-400 text-sm italic">Belum ada ulasan untuk event ini.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-12">
        <div class="space-y-4">
            <span class="px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">{{ $event->category->name }}</span>
            <h1 class="text-4xl md:text-5xl font-black leading-tight">{{ $event->title }}</h1>
            <div class="flex flex-wrap gap-6 text-slate-500 font-medium">
                <span>{{ $event->date->format('d M Y, H:i') }}</span>
                <span>{{ $event->location }}</span>
            </div>
        </div>

        <div>
            <h3 class="text-2xl font-bold mb-4">Deskripsi Event</h3>
            <p class="text-lg text-slate-600 leading-relaxed">{{ $event->description }}</p>
        </div>

        <div class="bg-indigo-600 rounded-3xl p-8 md:p-12 text-white shadow-2xl shadow-indigo-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div>
                    <p class="text-indigo-200 font-bold uppercase tracking-widest text-sm mb-2">Harga Tiket</p>
                    <h2 class="text-4xl md:text-5xl font-black">Rp {{ number_format($event->price, 0, ',', '.') }}</h2>
                    <p class="mt-4 text-indigo-100">Sisa stok: <span class="font-bold underline">{{ $event->stock }} tiket</span></p>
                </div>
                <a href="{{ route('checkout.create', $event->id) }}"
                    class="inline-block px-10 py-5 bg-white text-indigo-600 rounded-2xl font-black text-xl hover:scale-105 transition-transform shadow-xl">
                    Pesan Sekarang
                </a>
            </div>
        </div>

        <section class="space-y-6" id="ulasan">
            <h3 class="text-2xl font-bold">Rating &amp; Ulasan Peserta</h3>

            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-xl font-bold text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded-xl font-bold text-sm">{{ session('error') }}</div>
            @endif

            @if($canReview)
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                    <h4 class="font-bold mb-4">Bagikan pengalaman Anda</h4>
                    <form action="{{ route('reviews.store', $event->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                                    <span class="block text-3xl text-slate-300 peer-checked:text-amber-400 hover:text-amber-400">*</span>
                                </label>
                            @endfor
                        </div>
                        <textarea name="comment" rows="3" placeholder="Ceritakan pengalaman Anda di acara ini..."
                            class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition"></textarea>
                        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition">Kirim Ulasan</button>
                    </form>
                </div>
            @endif

            <div class="space-y-4">
                @forelse($reviews as $review)
                    <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $review->user->name }}</p>
                                    <p class="text-amber-400">{{ str_repeat('*', $review->rating) }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-slate-400">{{ $review->created_at->format('d M Y') }}</span>
                        </div>
                        @if($review->comment)
                            <p class="text-slate-600 mt-2">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-slate-400 italic">Belum ada ulasan. Ulasan muncul setelah peserta hadir dan event selesai.</p>
                @endforelse
            </div>

            {{ $reviews->links() }}
        </section>
    </div>
</main>
@endsection

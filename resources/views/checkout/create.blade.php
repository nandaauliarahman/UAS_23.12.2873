@extends('layouts.app')

@section('content')
<main class="max-w-3xl mx-auto px-6 py-20">
        <div class="mb-12">
            <a href="{{ route('events.show', $event->id) }}" class="text-indigo-600 font-bold flex items-center gap-2 mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Event
            </a>
            <h1 class="text-4xl font-extrabold">Checkout</h1>
            <p class="text-slate-500 mt-2">Lengkapi data Anda untuk mendapatkan tiket.</p>
        </div>

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">{{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 gap-8">
            <!-- Summary Card -->
            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                @auth
    @if(auth()->user()->role === 'user')
        <div class="flex items-center gap-4 mb-8 p-4 bg-indigo-50 rounded-2xl">
            <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=6366f1&color=fff' }}"
                class="w-12 h-12 rounded-full object-cover border-2 border-white shadow">
            <div>
                <p class="text-xs text-indigo-500 font-bold uppercase tracking-wide">Masuk sebagai</p>
                <p class="font-bold text-slate-800">{{ auth()->user()->name }}</p>
            </div>
        </div>
    @endif
@else
    <a href="{{ route('social.google.redirect', ['redirect' => route('checkout.create', $event->id)]) }}"
        class="flex items-center justify-center gap-3 w-full py-4 mb-4 bg-white border-2 border-slate-200 rounded-2xl font-bold hover:border-indigo-600 hover:text-indigo-600 transition shadow-sm">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.63h6.47c-.28 1.5-1.13 2.78-2.4 3.63v3.02h3.89c2.27-2.09 3.56-5.17 3.56-8.83z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.07 7.93-2.9l-3.89-3.02c-1.08.73-2.46 1.16-4.04 1.16-3.1 0-5.73-2.1-6.67-4.92H1.32v3.09C3.29 21.3 7.32 24 12 24z"/>
            <path fill="#FBBC05" d="M5.33 14.32A7.2 7.2 0 014.94 12c0-.8.14-1.58.39-2.32V6.59H1.32A11.96 11.96 0 000 12c0 1.93.46 3.76 1.32 5.41l4.01-3.09z"/>
            <path fill="#EA4335" d="M12 4.77c1.76 0 3.34.61 4.58 1.79l3.44-3.44C17.94 1.19 15.24 0 12 0 7.32 0 3.29 2.7 1.32 6.59l4.01 3.09C6.27 6.86 8.9 4.77 12 4.77z"/>
        </svg>
        Continue with Google
    </a>
    <div class="flex items-center gap-4 mb-6">
        <div class="flex-1 h-px bg-slate-200"></div>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">atau isi manual</span>
        <div class="flex-1 h-px bg-slate-200"></div>
    </div>
@endauth
                <h3 class="text-xl font-bold mb-6 border-b pb-4">Pesanan Anda</h3>
                <div class="flex gap-6 items-start">
                    <img src="{{ ($event->poster_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->poster_path))
                        ? asset('storage/' . $event->poster_path)
                        : 'https://placehold.co/200x200' }}" alt="{{ $event->title }}" class="w-24 h-24 rounded-2xl object-cover">
                    <div>
                        <h4 class="font-extrabold text-lg">{{ $event->title }}</h4>
                        <p class="text-slate-500">{{ $event->date->format('d M Y') }} &bull; {{ $event->location }}</p>
                        <p class="text-indigo-600 font-bold mt-2">1 x Rp {{ number_format($event->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @php
                    $isFreeEvent = $event->price <= 0;
                    $adminFee = $isFreeEvent ? 0 : 5000;
                @endphp
                <div class="mt-8 pt-6 border-t space-y-3">
                    <div class="flex justify-between text-slate-500">
                        <span>Harga Tiket</span>
                        <span>Rp {{ number_format($event->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>Biaya Layanan</span>
                        <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-2xl font-black mt-4 pt-4 border-t">
                        <span>Total Bayar</span>
                        <span class="text-indigo-600">Rp {{ number_format($event->price + $adminFee, 0, ',', '.') }}</span>
                    </div>
                    @if($isFreeEvent)
                        <p class="text-sm font-bold text-green-600">Acara gratis akan langsung menerbitkan E-Ticket tanpa Midtrans.</p>
                    @else
                        <p class="text-sm text-slate-500">Coba voucher demo: <strong>MAHASISWA50</strong> atau <strong>HEMAT10000</strong>.</p>
                    @endif
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <h3 class="text-xl font-bold mb-6">Data Pemesan (Tanpa Login)</h3>
                <form action="{{ route('checkout.store', $event->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                        <input type="text" name="customer_name" placeholder="Masukkan nama sesuai identitas"
                            class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                            required value="{{ old('customer_name', auth()->user()->name ?? '') }}">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Email Aktif</label>
                            <input type="email" name="customer_email" placeholder="contoh@gmail.com"
                                class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                                required value="{{ old('customer_email', auth()->user()->email ?? '') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">No. WhatsApp</label>
                            <input type="tel" name="customer_phone" placeholder="08xxxxxxx"
                                class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                                required value="{{ old('customer_phone') }}">
                        </div>
                    </div>
                    @unless($isFreeEvent)
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">Kode Voucher</label>
                            <input type="text" name="coupon_code" placeholder="MAHASISWA50"
                                class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium uppercase"
                                value="{{ old('coupon_code') }}">
                        </div>
                    @endunless
                    <button type="submit"
                        class="block text-center w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition-all">
                        {{ $isFreeEvent ? 'Ambil Tiket Gratis' : 'Lanjut Pembayaran' }}
                    </button>
                </form>
            </div>
        </div>
    </main>
@endsection

@extends('layouts.app')
@section('title', 'Pembayaran Berhasil')
@section('content')
<main class="max-w-3xl mx-auto px-6 py-20 text-center">
    <div class="bg-white rounded-3xl border border-slate-200 p-12 shadow-sm inline-block w-full max-w-md">
        <div class="w-24 h-24 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-black mb-4">Terima Kasih!</h2>
        <p class="text-slate-500 mb-6 leading-relaxed">
            Pesanan <strong>{{ $transaction->order_id }}</strong> berstatus <strong>{{ ucfirst($transaction->status) }}</strong>.
            @if($transaction->isPaid())
                E-Ticket sudah diterbitkan untuk <strong>{{ $transaction->customer_email }}</strong>.
            @else
                E-Ticket akan dikirim setelah pembayaran terkonfirmasi lunas.
            @endif
        </p>
        @if($transaction->isPaid())
            <div class="mb-8 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-3">QR Check-in</p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=170x170&data={{ urlencode($transaction->order_id) }}" alt="QR Code" class="mx-auto mb-3">
                <p class="font-mono font-black text-slate-700">{{ $transaction->order_id }}</p>
                @if($transaction->coupon_code)
                    <p class="text-sm text-green-600 font-bold mt-3">Voucher {{ $transaction->coupon_code }}: -Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</p>
                @endif
            </div>
        @endif
        <a href="{{ route('home') }}" class="inline-block px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">
            Kembali ke Beranda
        </a>
    </div>
</main>
@endsection

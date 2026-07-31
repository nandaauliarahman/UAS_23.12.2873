@extends('layouts.organizer')

@section('title', 'Dashboard Organizer')
@section('page_title', $tenant->name)
@section('page_subtitle', $tenant->is_approved ? 'Akun aktif dan dapat menerbitkan event.' : 'Menunggu persetujuan Superadmin.')

@section('content')
@unless($tenant->is_approved)
    <div class="mb-8 p-5 bg-amber-100 text-amber-800 rounded-2xl font-bold">
        Akun penyelenggara belum disetujui. Kamu tetap bisa melihat dashboard, tetapi belum bisa publish event baru.
    </div>
@endunless

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <p class="text-slate-400 text-sm font-bold uppercase">Pendapatan</p>
        <h2 class="text-3xl font-black mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
    </div>
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <p class="text-slate-400 text-sm font-bold uppercase">Tiket Terjual</p>
        <h2 class="text-3xl font-black mt-2">{{ number_format($ticketsSold, 0, ',', '.') }}</h2>
    </div>
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <p class="text-slate-400 text-sm font-bold uppercase">Event Aktif</p>
        <h2 class="text-3xl font-black mt-2">{{ $activeEvents }}</h2>
    </div>
</div>

<div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
    <div class="p-6 border-b flex justify-between items-center">
        <h3 class="font-black text-xl">Transaksi Terbaru</h3>
        <a href="{{ route('organizer.events.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold">Kelola Event</a>
    </div>
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-400 uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Order</th>
                <th class="px-6 py-4">Pembeli</th>
                <th class="px-6 py-4">Event</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($recentTransactions as $trx)
                <tr>
                    <td class="px-6 py-4 font-bold">{{ $trx->order_id }}</td>
                    <td class="px-6 py-4">{{ $trx->customer_name }}</td>
                    <td class="px-6 py-4">{{ $trx->event->title ?? '-' }}</td>
                    <td class="px-6 py-4">{{ ucfirst($trx->status) }}</td>
                    <td class="px-6 py-4 text-right font-black text-indigo-600">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

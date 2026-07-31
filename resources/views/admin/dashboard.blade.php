@extends('layouts.admin')
@section('title', 'Kelola Event - Admin')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan Transaksi')
@section('content')
<!-- Main Content -->


        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <p class="text-slate-400 text-sm font-bold uppercase mb-1">Total Pendapatan</p>
                <h3 class="text-2xl font-black">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                </div>
                <p class="text-slate-400 text-sm font-bold uppercase mb-1">Tiket Terjual</p>
                <h3 class="text-2xl font-black">{{ number_format($ticketsSold, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-slate-400 text-sm font-bold uppercase mb-1">Event Aktif</p>
                <h3 class="text-2xl font-black">{{ $activeEvents }} Event</h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-slate-400 text-sm font-bold uppercase mb-1">Pesanan Pending</p>
                <h3 class="text-2xl font-black">{{ $pendingOrders }} Pesanan</h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4 8 4v14M9 21v-6h6v6"></path>
                    </svg>
                </div>
                <p class="text-slate-400 text-sm font-bold uppercase mb-1">Tenant Review</p>
                <h3 class="text-2xl font-black">{{ $pendingTenants }} Akun</h3>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 mb-10">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h3 class="font-black text-xl">Grafik Pendapatan</h3>
                    <p class="text-slate-400 font-medium text-sm">Ringkasan 6 bulan terakhir untuk demonstrasi dashboard admin.</p>
                </div>
                <a href="{{ route('admin.tenants.index') }}" class="px-4 py-2 bg-sky-50 text-sky-600 rounded-xl font-bold">Review Tenant</a>
            </div>
            <div class="h-64 flex items-end gap-4 border-b border-slate-200 pb-4">
                @foreach($revenueChart as $bar)
                    @php($height = max(6, round(($bar['revenue'] / $maxRevenue) * 100)))
                    <div class="flex-1 flex flex-col items-center gap-3 h-full justify-end">
                        <div class="text-center">
                            <p class="text-xs font-black text-slate-700">Rp {{ number_format($bar['revenue'], 0, ',', '.') }}</p>
                            <p class="text-[10px] text-slate-400">{{ $bar['orders'] }} order</p>
                        </div>
                        <div class="w-full max-w-16 bg-indigo-600 rounded-t-xl" style="height: {{ $height }}%;"></div>
                    </div>
                @endforeach
            </div>
            <div class="grid grid-cols-6 gap-4 mt-3 text-center text-xs font-bold text-slate-400">
                @foreach($revenueChart as $bar)
                    <span>{{ $bar['label'] }}</span>
                @endforeach
            </div>
        </div>

        <!-- Latest Sales Table -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b flex justify-between items-center">
                <h3 class="font-black text-xl">Transaksi Terakhir</h3>
                <a href="{{ route('admin.transactions.index') }}" class="text-indigo-600 font-bold hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4">Tgl Transaksi</th>
                            <th class="px-8 py-4">Pembeli</th>
                            <th class="px-8 py-4">Event</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-t">
                        @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-6 text-sm text-slate-600">
                                {{ $trx->created_at->format('d M y - H:i') }}<br>
                                <span class="text-xs text-slate-400">{{ $trx->order_id }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="font-bold uppercase tracking-wide text-sm">{{ $trx->customer_name }}</p>
                                <p class="text-xs text-slate-400">{{ $trx->customer_email }}</p>
                            </td>
                            <td class="px-8 py-6 font-medium text-slate-600">{{ $trx->event->title ?? '-' }}</td>
                            <td class="px-8 py-6">
                                @if($trx->status === 'settlement' || $trx->status === 'success')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold uppercase">Success</span>
                                @elseif($trx->status === 'pending')
                                    <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold uppercase">Pending</span>
                                @else
                                    <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-bold uppercase">{{ $trx->status }}</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 font-black text-indigo-600 text-right">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-slate-500">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

@endsection

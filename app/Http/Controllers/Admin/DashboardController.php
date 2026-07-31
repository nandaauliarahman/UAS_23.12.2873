<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index()
    {
        // 1. Menjumlahkan semua nominal total_price dari kolom Transaksi Lunas
        $totalRevenue = Transaction::whereIn('status', ['settlement', 'success'])->sum('total_price');

        // 2. Menghitung berapa orang tamu yang tiketnya sudah lunas
        $ticketsSold = Transaction::whereIn('status', ['settlement', 'success'])->count();

        // 3. Menghitung jumlah acara mendatang yang aktif diselenggarakan
        $activeEvents = Event::where('date', '>=', now())->count();

        // 4. Menghitung transaksi yang masih pending
        $pendingOrders = Transaction::where('status', 'pending')->count();

        $pendingTenants = Tenant::where('is_approved', false)->count();

        // 5. Menyertakan 5 daftar riwayat pesanan (history) paling mutakhir
        $recentTransactions = Transaction::with('event')->latest()->take(5)->get();

        $revenueChart = collect(range(5, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);

            return [
                'label' => $date->format('M'),
                'revenue' => Transaction::whereIn('status', ['settlement', 'success'])
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('total_price'),
                'orders' => Transaction::whereIn('status', ['settlement', 'success'])
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });
        $maxRevenue = max(1, (int) $revenueChart->max('revenue'));

        return view('admin.dashboard', compact(
            'totalRevenue', 'ticketsSold', 'activeEvents', 'pendingOrders', 'pendingTenants',
            'recentTransactions', 'revenueChart', 'maxRevenue'
        ));
    }

    function indexTransaction()
    {
        $transactions = \App\Models\Transaction::with('event')->latest()->paginate(20);
        return view('admin.transactions', compact('transactions'));
    }
}

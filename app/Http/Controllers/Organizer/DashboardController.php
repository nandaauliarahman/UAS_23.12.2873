<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $eventIds = $tenant->events()->pluck('id');

        $totalRevenue = Transaction::whereIn('event_id', $eventIds)
            ->whereIn('status', ['settlement', 'success'])->sum('total_price');

        $ticketsSold = Transaction::whereIn('event_id', $eventIds)
            ->whereIn('status', ['settlement', 'success'])->count();

        $activeEvents = $tenant->events()->where('date', '>=', now())->count();

        $recentTransactions = Transaction::with('event')
            ->whereIn('event_id', $eventIds)->latest()->take(5)->get();

        return view('organizer.dashboard', compact('tenant', 'totalRevenue', 'ticketsSold', 'activeEvents', 'recentTransactions'));
    }
}
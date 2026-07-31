<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    function index()
    {

    }

    function show(Event $event)
    {
        $event->load(['category', 'tenant']);

        if ($event->tenant_id && ! $event->tenant?->is_approved) {
            abort(404);
        }

        $reviews = $event->reviews()->with('user')->latest()->paginate(5);
        $averageRating = $event->averageRating();
        $reviewCount = $event->reviews()->count();

        $canReview = false;
        if (auth()->check() && $event->hasEnded()) {
            $canReview = \App\Models\Transaction::where('event_id', $event->id)
                ->whereIn('status', ['settlement', 'success'])
                ->where('customer_email', auth()->user()->email)
                ->whereDoesntHave('review')
                ->exists();
        }

        return view('event-detail', compact('event', 'reviews', 'averageRating', 'reviewCount', 'canReview'));
    }

    function checkout()
    {
        return view('checkout');
    }

    function ticket()
    {
        return view('ticket');
    }
}

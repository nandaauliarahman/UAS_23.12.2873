<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Event $event)
    {
        if (!auth()->check()) {
            return redirect()->route('social.google.redirect', [
                'redirect' => route('events.show', $event->id),
            ])->with('error', 'Silakan login dengan Google terlebih dahulu untuk memberi ulasan.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $eligibleTransaction = $this->eligibleTransactionFor($event, auth()->user());

        if (!$eligibleTransaction) {
            return back()->with('error', 'Anda hanya bisa memberi ulasan untuk acara yang tiketnya sudah lunas dan telah selesai berlangsung.');
        }

        if ($eligibleTransaction->review()->exists()) {
            return back()->with('error', 'Anda sudah pernah memberikan ulasan untuk tiket ini.');
        }

        Review::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'transaction_id' => $eligibleTransaction->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }

    private function eligibleTransactionFor(Event $event, $user): ?Transaction
    {
        if (!$event->hasEnded()) {
            return null;
        }

        return Transaction::where('event_id', $event->id)
            ->whereIn('status', ['settlement', 'success'])
            ->where('customer_email', $user->email)
            ->whereDoesntHave('review')
            ->latest()
            ->first();
    }
}
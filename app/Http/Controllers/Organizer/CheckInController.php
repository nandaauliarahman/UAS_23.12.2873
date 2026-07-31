<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;

        $recentCheckIns = Transaction::with('event')
            ->whereHas('event', fn ($query) => $query->where('tenant_id', $tenant->id))
            ->whereNotNull('checked_in_at')
            ->latest('checked_in_at')
            ->take(10)
            ->get();

        return view('organizer.check-in.index', compact('tenant', 'recentCheckIns'));
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|string|max:255',
        ]);

        $tenant = auth()->user()->tenant;
        $transaction = Transaction::with('event')
            ->where('order_id', trim($data['order_id']))
            ->first();

        $result = $this->validateTicket($transaction, $tenant->id);

        if ($result['ok']) {
            $transaction->update([
                'checked_in_at' => now(),
                'checked_in_by' => auth()->id(),
            ]);

            $result['checked_in_at'] = $transaction->fresh()->checked_in_at->format('d M Y H:i');
        }

        if ($request->expectsJson()) {
            return response()->json($result, $result['ok'] ? 200 : 422);
        }

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    private function validateTicket(?Transaction $transaction, int $tenantId): array
    {
        if (! $transaction) {
            return ['ok' => false, 'message' => 'Tiket tidak ditemukan.'];
        }

        if (! $transaction->event || $transaction->event->tenant_id !== $tenantId) {
            return ['ok' => false, 'message' => 'Tiket ini bukan untuk event milik penyelenggara Anda.'];
        }

        if (! $transaction->isPaid()) {
            return ['ok' => false, 'message' => 'Tiket belum lunas, check-in ditolak.'];
        }

        if ($transaction->checked_in_at) {
            return [
                'ok' => false,
                'message' => 'Tiket sudah pernah digunakan pada ' . $transaction->checked_in_at->format('d M Y H:i') . '.',
            ];
        }

        return [
            'ok' => true,
            'message' => 'Check-in berhasil untuk ' . $transaction->customer_name . ' - ' . $transaction->event->title . '.',
            'customer_name' => $transaction->customer_name,
            'event_title' => $transaction->event->title,
            'order_id' => $transaction->order_id,
        ];
    }
}

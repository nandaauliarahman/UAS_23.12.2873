<?php

namespace App\Http\Controllers;

use App\Mail\EventTicketMail;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        return view('checkout.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        // 1. Validasi Input Kredensial Pelanggan
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        // 2. Cegah Check-out Jika Tiket Habis
        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX (Unik)
        $orderId = 'TRX-' . time() . '-' . Str::random(5);
        [$couponCode, $discountAmount] = $this->resolveCoupon($request->coupon_code, (int) $event->price);
        $subtotal = max(0, (int) $event->price - $discountAmount);
        $adminFee = $subtotal > 0 ? 5000 : 0;
        $totalPrice = $subtotal + $adminFee;

        // 4. Merekam Transaksi ke Database
        $transaction = Transaction::create([
            'event_id' => $event->id,
            'order_id' => $orderId,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'coupon_code' => $couponCode,
            'discount_amount' => $discountAmount,
            'total_price' => $totalPrice,
            'status' => 'pending', // Status Awal
        ]);

        if ($totalPrice <= 0) {
            $this->markAsPaidAndIssueTicket($transaction);

            return redirect()->route('checkout.success', $transaction->order_id)
                ->with('success', 'Event gratis berhasil dipesan. E-Ticket langsung diterbitkan.');
        }

        // --- INTEGRASI SNAP MIDTRANS ---

        $serverKey = trim((string) env('MIDTRANS_SERVER_KEY'));

        // Keep the hosted UAS demo usable without exposing payment credentials.
        if ($serverKey === '') {
            $this->markAsPaidAndIssueTicket($transaction);

            return redirect()->route('checkout.success', $transaction->order_id)
                ->with('success', 'Pembayaran demo berhasil. E-Ticket langsung diterbitkan.');
        }

        // Konfigurasi Kredensial Environment Midtrans
        \Midtrans\Config::$serverKey = $serverKey;
        \Midtrans\Config::$isProduction = false; // Mode Sandbox!
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Susun Paket Array Data Transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ],
        ];

        try {
            // Perintah Tembak Generate Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Update rekaman kita bahwa transaksi terkait sudah memiliki id token pelunasan
            $transaction->update(['snap_token' => $snapToken]);

            // Redirect ke halaman antarmuka pembayaran final pelanggan
            return redirect()->route('checkout.payment', $transaction->order_id);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran jaringan: ' . $e->getMessage());
        }
    }

    public function payment($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        if ($transaction->isPaid() && ! $transaction->snap_token) {
            return view('checkout.success', compact('transaction', 'categories'));
        }
        return view('checkout.payment', compact('transaction', 'categories'));
    }

    public function success($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        if (trim((string) env('MIDTRANS_SERVER_KEY')) === '') {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        // Konfigurasi Midtrans untuk mengecek status transaksi langsung ke API
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        try {
            // Mengecek status pesanan secara mandiri (Bypass / Fallback Check)
            $status = \Midtrans\Transaction::status($order_id);

            if ($status) {
                // Mengambil nilai status transaksi
                $trx_status = is_array($status) ? ($status['transaction_status'] ?? '') : ($status->transaction_status ?? '');

                // Jika API Midtrans mengonfirmasi bahwa transaksi telah berhasil (settlement / capture)
                if (in_array($trx_status, ['settlement', 'capture'])) {
                    // Hanya lakukan update jika status di database lokal masih 'pending' (indikasi Webhook tidak masuk)
                    if (strtolower($transaction->status) === 'pending') {
                        $transaction->update(['status' => 'success']);

                        if ($transaction->event && $transaction->event->stock > 0) {
                            $transaction->event->stock = $transaction->event->stock - 1;
                            $transaction->event->save();

                            try {
                                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                                    ->send(new \App\Mail\EventTicketMail($transaction));
                            } catch (\Exception $e) {
                                \Illuminate\Support\Facades\Log::error('Gagal mengirim email E-Ticket secara manual (Bypass): ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika terjadi error dari API Midtrans (transaksi tidak valid), kembalikan ke beranda
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }

    private function resolveCoupon(?string $code, int $subtotal): array
    {
        $code = strtoupper(trim((string) $code));

        if ($code === '' || $subtotal <= 0) {
            return [null, 0];
        }

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || ! $coupon->isUsable()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Kode voucher tidak valid atau sudah tidak aktif.',
            ]);
        }

        return [$coupon->code, $coupon->discountFor($subtotal)];
    }

    private function markAsPaidAndIssueTicket(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $transaction->load('event');

            if ($transaction->isPaid()) {
                return;
            }

            $event = Event::whereKey($transaction->event_id)->lockForUpdate()->first();

            if (! $event || $event->stock <= 0) {
                throw ValidationException::withMessages([
                    'stock' => 'Stok tiket sudah habis.',
                ]);
            }

            $event->decrement('stock');
            $transaction->update(['status' => 'success']);
        });

        try {
            Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction->fresh('event')));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim e-ticket gratis: ' . $e->getMessage());
        }
    }
}

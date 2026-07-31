@extends('layouts.organizer')

@section('title', 'Check-in Scanner')
@section('page_title', 'Check-in Scanner')
@section('page_subtitle', 'Scan QR e-ticket peserta dan tandai sebagai used.')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
        <div id="reader" class="w-full overflow-hidden rounded-2xl bg-slate-100 min-h-80 flex items-center justify-center"></div>

        <div id="scan-result" class="mt-6 p-5 bg-slate-50 border border-slate-100 rounded-2xl font-bold text-slate-600">
            Arahkan kamera ke QR e-ticket.
        </div>

        <form id="manual-form" action="{{ route('organizer.check-in.verify') }}" method="POST" class="mt-6 flex gap-3">
            @csrf
            <input id="manual-order-id" name="order_id" placeholder="Masukkan Order ID manual"
                class="flex-1 px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
            <button class="px-6 py-4 bg-indigo-600 text-white rounded-2xl font-black">Check-in</button>
        </form>
    </div>

    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
        <h3 class="text-xl font-black mb-5">Check-in Terakhir</h3>
        <div class="space-y-4">
            @forelse($recentCheckIns as $trx)
                <div class="p-4 bg-slate-50 rounded-2xl">
                    <p class="font-black">{{ $trx->customer_name }}</p>
                    <p class="text-sm text-slate-500">{{ $trx->event->title ?? '-' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $trx->checked_in_at->format('d M Y H:i') }}</p>
                </div>
            @empty
                <p class="text-slate-400 italic">Belum ada peserta check-in.</p>
            @endforelse
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    const resultBox = document.getElementById('scan-result');
    const csrf = '{{ csrf_token() }}';
    let lastCode = '';
    let lock = false;

    async function verifyTicket(orderId) {
        if (!orderId || lock || orderId === lastCode) {
            return;
        }

        lock = true;
        lastCode = orderId;
        resultBox.className = 'mt-6 p-5 bg-indigo-50 border border-indigo-100 rounded-2xl font-bold text-indigo-700';
        resultBox.textContent = 'Memeriksa tiket ' + orderId + '...';

        try {
            const response = await fetch('{{ route('organizer.check-in.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ order_id: orderId })
            });
            const data = await response.json();
            resultBox.className = data.ok
                ? 'mt-6 p-5 bg-green-100 border border-green-200 rounded-2xl font-bold text-green-700'
                : 'mt-6 p-5 bg-red-100 border border-red-200 rounded-2xl font-bold text-red-700';
            resultBox.textContent = data.message;
        } catch (error) {
            resultBox.className = 'mt-6 p-5 bg-red-100 border border-red-200 rounded-2xl font-bold text-red-700';
            resultBox.textContent = 'Scanner gagal menghubungi server.';
        }

        setTimeout(() => {
            lock = false;
            lastCode = '';
        }, 2500);
    }

    document.getElementById('manual-form').addEventListener('submit', function (event) {
        event.preventDefault();
        verifyTicket(document.getElementById('manual-order-id').value.trim());
    });

    if (window.Html5QrcodeScanner) {
        const scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 250 });
        scanner.render((decodedText) => verifyTicket(decodedText));
    } else {
        resultBox.textContent = 'Library scanner tidak tersedia. Gunakan input manual.';
    }
</script>
@endsection

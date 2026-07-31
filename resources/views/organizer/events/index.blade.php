@extends('layouts.organizer')

@section('title', 'Event Organizer')
@section('page_title', 'Event Saya')
@section('page_subtitle', 'Data event hanya menampilkan milik tenant kamu.')

@section('content')
<div class="mb-6 flex justify-end">
    <a href="{{ route('organizer.events.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black">Tambah Event</a>
</div>

<div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-400 uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Event</th>
                <th class="px-6 py-4">Kategori</th>
                <th class="px-6 py-4">Tanggal</th>
                <th class="px-6 py-4">Harga/Stok</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($events as $event)
                <tr>
                    <td class="px-6 py-5 font-black">{{ $event->title }}</td>
                    <td class="px-6 py-5">{{ $event->category->name ?? '-' }}</td>
                    <td class="px-6 py-5">{{ $event->date->format('d M Y H:i') }}</td>
                    <td class="px-6 py-5">
                        <p class="font-bold text-indigo-600">Rp {{ number_format($event->price, 0, ',', '.') }}</p>
                        <p class="text-sm text-slate-500">Stok {{ $event->stock }}</p>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('organizer.events.edit', $event->id) }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold">Edit</a>
                            <form action="{{ route('organizer.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Hapus event ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-4 py-2 bg-rose-50 text-rose-600 rounded-xl font-bold">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada event.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-5 bg-slate-50 border-t">{{ $events->links() }}</div>
</div>
@endsection

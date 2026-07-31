@extends('layouts.admin')

@section('title', 'Penyelenggara')
@section('page_title', 'Penyelenggara')
@section('page_subtitle', 'Review dan setujui akun kepanitiaan/HIMA.')

@section('content')
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50 text-slate-400 uppercase text-xs">
            <tr>
                <th class="px-6 py-4">Penyelenggara</th>
                <th class="px-6 py-4">Owner</th>
                <th class="px-6 py-4">Event</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($tenants as $tenant)
                <tr>
                    <td class="px-6 py-5">
                        <p class="font-black">{{ $tenant->name }}</p>
                        <p class="text-sm text-slate-500">{{ $tenant->description ?: '-' }}</p>
                    </td>
                    <td class="px-6 py-5">
                        <p class="font-bold">{{ $tenant->owner->name ?? '-' }}</p>
                        <p class="text-sm text-slate-500">{{ $tenant->owner->email ?? '-' }}</p>
                    </td>
                    <td class="px-6 py-5">{{ $tenant->events_count }}</td>
                    <td class="px-6 py-5">
                        @if($tenant->is_approved)
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold uppercase">Approved</span>
                        @else
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-bold uppercase">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex justify-end gap-2">
                            <form action="{{ route('admin.tenants.approve', $tenant->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="px-4 py-2 bg-green-600 text-white rounded-xl font-bold">Approve</button>
                            </form>
                            <form action="{{ route('admin.tenants.reject', $tenant->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button class="px-4 py-2 bg-slate-100 text-slate-700 rounded-xl font-bold">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-10 text-center text-slate-500">Belum ada penyelenggara.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-5 bg-slate-50 border-t">{{ $tenants->links() }}</div>
</div>
@endsection

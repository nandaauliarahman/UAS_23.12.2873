@extends('layouts.organizer')

@section('title', 'Tambah Event')
@section('page_title', 'Tambah Event')
@section('page_subtitle', 'Event baru otomatis terhubung ke tenant kamu.')

@section('content')
<div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm max-w-3xl">
    <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @include('organizer.events.partials.form', ['buttonText' => 'Simpan Event'])
    </form>
</div>
@endsection

@extends('layouts.organizer')

@section('title', 'Edit Event')
@section('page_title', 'Edit Event')
@section('page_subtitle', 'Perubahan hanya berlaku untuk event milik tenant kamu.')

@section('content')
<div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm max-w-3xl">
    <form action="{{ route('organizer.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')
        @include('organizer.events.partials.form', ['buttonText' => 'Update Event'])
    </form>
</div>
@endsection

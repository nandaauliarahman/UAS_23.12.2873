<div>
    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Kategori</label>
    <select name="category_id" required class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
        <option value="">Pilih kategori</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $event->category_id ?? '') == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Judul</label>
    <input name="title" value="{{ old('title', $event->title ?? '') }}" required class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
</div>

<div>
    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Deskripsi</label>
    <textarea name="description" rows="4" class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Tanggal</label>
        <input name="date" type="datetime-local" value="{{ old('date', isset($event) ? $event->date->format('Y-m-d\TH:i') : '') }}" required class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
    </div>
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Lokasi</label>
        <input name="location" value="{{ old('location', $event->location ?? '') }}" required class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Harga</label>
        <input name="price" type="number" min="0" value="{{ old('price', $event->price ?? 0) }}" required class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
    </div>
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Stok</label>
        <input name="stock" type="number" min="1" value="{{ old('stock', $event->stock ?? 1) }}" required class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
    </div>
</div>

<div>
    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase">Poster</label>
    <input name="poster" type="file" accept="image/*" class="w-full px-5 py-4 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-600">
</div>

<div class="flex gap-3 pt-3">
    <button class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-black">{{ $buttonText }}</button>
    <a href="{{ route('organizer.events.index') }}" class="px-6 py-3 bg-slate-100 text-slate-700 rounded-2xl font-bold">Batal</a>
</div>

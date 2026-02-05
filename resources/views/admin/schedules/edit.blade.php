@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Atur Jadwal Kerja: {{ $user->name }}</h2>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.schedules.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Jadwal Masuk</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $user->start_time) }}" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" required>
                        @error('start_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Waktu standar masuk kerja.</p>
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Jadwal Pulang</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $user->end_time) }}" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" required>
                        @error('end_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Waktu standar pulang kerja.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-sm">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

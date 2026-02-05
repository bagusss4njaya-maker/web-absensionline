@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Edit Absensi Pegawai</h2>
        </div>

        <div class="p-6">
            <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai</label>
                    <input type="text" value="{{ $attendance->user->name }}" class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2 border" disabled>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="text" value="{{ \Carbon\Carbon::parse($attendance->date)->locale('id')->isoFormat('D MMMM Y') }}" class="w-full rounded-md border-gray-300 bg-gray-100 shadow-sm p-2 border" disabled>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="clock_in" class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk</label>
                        <input type="time" name="clock_in" id="clock_in" value="{{ $attendance->clock_in }}" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required>
                        @error('clock_in')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="clock_out" class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang</label>
                        <input type="time" name="clock_out" id="clock_out" value="{{ $attendance->clock_out }}" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
                        @error('clock_out')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

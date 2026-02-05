@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Tambah Pegawai Baru</h2>
        </div>
        <div class="p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.employees.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border" required>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border" required>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border" required>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('admin.employees.index') }}" class="mr-3 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

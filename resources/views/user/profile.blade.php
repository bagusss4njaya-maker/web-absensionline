@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-white text-2xl font-bold">Profil Saya</h2>
        </div>
        
        <div class="p-6">
            <form action="{{ route('user.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informasi Dasar -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Informasi Akun</h3>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Peran</label>
                            <input type="text" value="{{ ucfirst($user->role) }}" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 shadow-sm sm:text-sm border p-2 cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Ganti Password -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Ganti Kata Sandi (Opsional)</h3>
                        
                        <div class="relative">
                            <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                            <div class="relative">
                                <input type="password" name="password" id="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2 pr-10">
                                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none z-20">
                                    <svg id="eye-open-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eye-closed-password" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2 pr-10">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none z-20">
                                    <svg id="eye-open-password_confirmation" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eye-closed-password_confirmation" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Informasi Jadwal -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-6">
        <div class="bg-gray-100 px-6 py-4 border-b">
            <h3 class="text-lg font-bold text-gray-800">Jadwal Kerja</h3>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
                <p class="text-sm text-gray-500 uppercase font-semibold">Jam Masuk</p>
                <p class="text-xl font-bold text-green-700">{{ $user->start_time ?? '-' }}</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg border border-red-200">
                <p class="text-sm text-gray-500 uppercase font-semibold">Jam Pulang</p>
                <p class="text-xl font-bold text-red-700">{{ $user->end_time ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const eyeOpen = document.getElementById('eye-open-' + inputId);
        const eyeClosed = document.getElementById('eye-closed-' + inputId);
        
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
@endsection

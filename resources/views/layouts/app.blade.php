<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @auth
    <nav class="bg-white shadow mb-4">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="/" class="text-xl font-bold text-gray-800 mr-8">Absensi Online</a>
                @if(Auth::user()->role === 'admin')
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
                        <a href="{{ route('admin.employees.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Pegawai</a>
                        <a href="{{ route('admin.attendances.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Data Absensi</a>
                        <a href="{{ route('admin.schedules.index') }}" class="text-gray-600 hover:text-blue-600 font-medium">Jadwal Kerja</a>
                    </div>
                @else
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('user.dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Dashboard</a>
                        <a href="{{ route('user.profile') }}" class="text-gray-600 hover:text-blue-600 font-medium">Profil</a>
                    </div>
                @endif
            </div>
            <div>
                <span class="mr-4">Halo, {{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline">Keluar</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth
    <div class="max-w-7xl mx-auto px-4">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</body>
</html>

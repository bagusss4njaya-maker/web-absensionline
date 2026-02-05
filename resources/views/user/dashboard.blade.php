@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Absensi Hari Ini</h2>

        <div class="mb-6 text-center bg-gray-50 p-4 rounded-lg border border-gray-200">
             <div class="text-gray-500 text-sm mb-1">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
             <div class="text-4xl font-mono font-bold text-gray-800" id="user-clock">
                {{ \Carbon\Carbon::now()->format('H:i:s') }}
             </div>
             <div class="text-xs text-gray-400 mt-1">Waktu Indonesia Barat (Jakarta)</div>
             <div class="mt-4 pt-4 border-t border-gray-200 flex justify-center gap-6">
                <div class="text-center">
                    <span class="block text-xs text-gray-500 uppercase font-semibold tracking-wider">Jadwal Masuk</span>
                    <span class="text-lg font-bold text-green-600">{{ Auth::user()->start_time }}</span>
                </div>
                <div class="text-center">
                    <span class="block text-xs text-gray-500 uppercase font-semibold tracking-wider">Jadwal Pulang</span>
                    <span class="text-lg font-bold text-red-600">{{ Auth::user()->end_time }}</span>
                </div>
             </div>
        </div>

        <script>
            const scheduleEndTime = "{{ Auth::user()->end_time }}";

            function updateUserClock() {
                const now = new Date();
                const options = { 
                    timeZone: 'Asia/Jakarta', 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit',
                    hour12: false 
                };
                const formatter = new Intl.DateTimeFormat('en-GB', options);
                const timeString = formatter.format(now);
                
                document.getElementById('user-clock').textContent = timeString;

                // Update Clock Out Button State
                const btnOut = document.getElementById('clock-out-btn');
                if (btnOut) {
                    if (timeString < scheduleEndTime) {
                        btnOut.disabled = true;
                        btnOut.classList.remove('bg-red-500', 'hover:bg-red-600');
                        btnOut.classList.add('bg-gray-400', 'cursor-not-allowed');
                        btnOut.innerText = `Belum Waktunya Pulang (Jadwal: ${scheduleEndTime})`;
                    } else {
                        btnOut.disabled = false;
                        btnOut.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        btnOut.classList.add('bg-red-500', 'hover:bg-red-600');
                        btnOut.innerText = 'Pulang (Clock Out)';
                    }
                }

                // Update Clock In Button State / Absent Status
                const btnIn = document.getElementById('clock-in-btn');
                const clockInContainer = document.getElementById('clock-in-container');
                const absentStatus = document.getElementById('absent-status');

                if (btnIn) {
                    if (timeString >= scheduleEndTime) {
                        // Option 1: Disable button (Previous)
                        // btnIn.disabled = true;
                        // btnIn.classList.remove('bg-green-500', 'hover:bg-green-600');
                        // btnIn.classList.add('bg-gray-400', 'cursor-not-allowed');
                        // btnIn.innerText = 'Sudah Lewat Jadwal Pulang';
                        
                        // Option 2: Show Absent Status (New Request)
                        if (clockInContainer) clockInContainer.classList.add('hidden');
                        if (absentStatus) absentStatus.classList.remove('hidden');
                    } else {
                        // Ensure button is visible if within time
                        if (clockInContainer) clockInContainer.classList.remove('hidden');
                        if (absentStatus) absentStatus.classList.add('hidden');

                        btnIn.disabled = false;
                        btnIn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        btnIn.classList.add('bg-green-500', 'hover:bg-green-600');
                        btnIn.innerText = 'Masuk (Clock In)';
                    }
                }
            }
            setInterval(updateUserClock, 1000);
            // Run immediately to set initial state
            document.addEventListener('DOMContentLoaded', updateUserClock);
        </script>

        <div class="flex flex-col space-y-4">
            @if(!$todayAttendance)
                <div id="clock-in-container">
                    <form action="{{ route('clock.in') }}" method="POST">
                        @csrf
                        <button type="submit" id="clock-in-btn" class="w-full bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600 font-bold text-lg shadow">Masuk (Clock In)</button>
                    </form>
                </div>
                <div id="absent-status" class="hidden p-6 bg-red-50 border border-red-200 rounded-lg text-center shadow-inner">
                    <div class="flex flex-col items-center justify-center text-red-600">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-bold">Tidak Hadir</h3>
                        <p class="text-sm mt-1">Anda telah melewati batas waktu jam pulang.</p>
                    </div>
                </div>
            @elseif(!$todayAttendance->clock_out)
                <div class="p-4 bg-green-50 border border-green-200 rounded text-green-800 mb-4">
                    <p class="font-semibold">Anda sudah absen masuk pada:</p>
                    <p class="text-2xl font-bold">{{ $todayAttendance->clock_in }}</p>
                </div>
                <form action="{{ route('clock.out') }}" method="POST">
                    @csrf
                    <button type="submit" id="clock-out-btn" class="w-full bg-red-500 text-white px-6 py-3 rounded hover:bg-red-600 font-bold text-lg shadow">Pulang (Clock Out)</button>
                </form>
            @else
                <div class="p-4 bg-blue-50 border border-blue-200 rounded text-blue-800">
                    <div class="mb-2">
                        <span class="font-semibold">Jam Masuk:</span> {{ $todayAttendance->clock_in }}
                    </div>
                    <div class="mb-2">
                        <span class="font-semibold">Jam Pulang:</span> {{ $todayAttendance->clock_out }}
                    </div>
                    <div class="text-green-600 font-bold mt-4 text-center border-t border-blue-200 pt-2">Absensi Selesai Hari Ini</div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Riwayat Saya</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b text-left">Tanggal</th>
                        <th class="py-2 px-4 border-b text-left">Masuk</th>
                        <th class="py-2 px-4 border-b text-left">Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($attendance->date)->locale('id')->isoFormat('D MMMM Y') }}</td>
                        <td class="py-2 px-4 border-b text-green-600 font-medium">{{ $attendance->clock_in }}</td>
                        <td class="py-2 px-4 border-b text-red-600 font-medium">{{ $attendance->clock_out ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 px-4 text-center text-gray-500">Belum ada riwayat absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

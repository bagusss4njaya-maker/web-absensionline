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
                const btn = document.getElementById('clock-out-btn');
                if (btn) {
                    if (timeString < scheduleEndTime) {
                        btn.disabled = true;
                        btn.classList.remove('bg-red-500', 'hover:bg-red-600');
                        btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                        btn.innerText = `Belum Waktunya Pulang (Jadwal: ${scheduleEndTime})`;
                    } else {
                        btn.disabled = false;
                        btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                        btn.classList.add('bg-red-500', 'hover:bg-red-600');
                        btn.innerText = 'Pulang (Clock Out)';
                    }
                }
            }
            setInterval(updateUserClock, 1000);
            // Run immediately to set initial state
            document.addEventListener('DOMContentLoaded', updateUserClock);
        </script>

        <div class="flex flex-col space-y-4">
            @if(!$todayAttendance)
                <form action="{{ route('clock.in') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-green-500 text-white px-6 py-3 rounded hover:bg-green-600 font-bold text-lg shadow">Masuk (Clock In)</button>
                </form>
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

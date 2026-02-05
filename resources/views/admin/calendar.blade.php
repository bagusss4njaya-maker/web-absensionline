@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header & Legend -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Kalender Kehadiran Tahun {{ \Carbon\Carbon::now()->year }}</h2>
            <div class="flex gap-4 text-sm text-gray-600 mt-2 md:mt-0">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div> <span>Tepat Waktu</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div> <span>Telat &le; 30m</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div> <span>Telat &gt; 30m</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-gray-800"></div> <span>Tidak Hadir</span>
                </div>
            </div>
        </div>
    </div>

    @php
        // Group attendances by date (Y-m-d) for easier access
        $attendancesByDate = $attendances->groupBy('date');
    @endphp

    <!-- Calendar Grid 12 Months -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach(range(1, 12) as $month)
            @php
                $startOfMonth = \Carbon\Carbon::createFromDate(now()->year, $month, 1);
                $daysInMonth = $startOfMonth->daysInMonth;
                $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Minggu) - 6 (Sabtu)
            @endphp

            <div class="bg-white rounded-xl shadow-md overflow-hidden p-4">
                <h3 class="text-center font-bold text-lg text-gray-800 mb-4 border-b pb-2">
                    {{ $startOfMonth->locale('id')->isoFormat('MMMM') }}
                </h3>

                <div class="grid grid-cols-7 gap-1 mb-2 text-center font-medium text-gray-500 text-xs">
                    <div>Min</div>
                    <div>Sen</div>
                    <div>Sel</div>
                    <div>Rab</div>
                    <div>Kam</div>
                    <div>Jum</div>
                    <div>Sab</div>
                </div>

                <div class="grid grid-cols-7 gap-1">
                    {{-- Empty cells for previous month --}}
                    @for ($i = 0; $i < $firstDayOfWeek; $i++)
                        <div class="h-16 bg-gray-50 rounded p-1"></div>
                    @endfor

                    {{-- Days of current month --}}
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentDate = $startOfMonth->copy()->addDays($day - 1)->format('Y-m-d');
                            $daysAttendances = $attendancesByDate->get($currentDate, collect());
                            $isToday = $currentDate === \Carbon\Carbon::now()->format('Y-m-d');
                            
                            // Prepare data for JS
                            $jsData = $daysAttendances->map(function($att) {
                                $color = 'text-green-600 bg-green-100'; 
                                $category = 'ontime';
                                $statusText = 'Tepat Waktu';
                                
                                if ($att->user->start_time && $att->clock_in) {
                                    $attendanceDate = \Carbon\Carbon::parse($att->date)->format('Y-m-d');
                                    $startTime = \Carbon\Carbon::parse($attendanceDate.' '.$att->user->start_time);
                                    $clockIn = \Carbon\Carbon::parse($attendanceDate.' '.$att->clock_in);
                                    $diffInSeconds = $startTime->diffInSeconds($clockIn, false);
                                    $lateSeconds = max(0, $diffInSeconds);
                                    $lateMinutes = intdiv($lateSeconds, 60);
                                    $hours = intdiv($lateSeconds, 3600);
                                    $minutes = intdiv($lateSeconds % 3600, 60);
                                    $seconds = $lateSeconds % 60;
                                    $formatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                    if ($lateMinutes > 0 && $lateMinutes <= 30) {
                                        $color = 'text-yellow-700 bg-yellow-100';
                                        $category = 'late_under_30';
                                        $statusText = 'Telat ' . $formatted;
                                    } elseif ($lateMinutes > 30) {
                                        $color = 'text-red-700 bg-red-100';
                                        $category = 'late_over_30';
                                        $statusText = 'Telat ' . $formatted;
                                    }
                                }
                                return [
                                    'name' => $att->user->name,
                                    'clock_in' => $att->clock_in,
                                    'status' => $statusText,
                                    'category' => $category,
                                    'color_class' => $color
                                ];
                            });

                            // Add Absent Users
                            $presentUserIds = $daysAttendances->pluck('user_id')->toArray();
                            $absentUsers = $users->whereNotIn('id', $presentUserIds);
                            
                            $absentData = $absentUsers->map(function($user) use ($currentDate) {
                                $isAbsent = false;
                                $today = \Carbon\Carbon::now()->format('Y-m-d');
                                
                                if ($currentDate < $today) {
                                    $isAbsent = true;
                                } elseif ($currentDate === $today) {
                                    if (now()->format('H:i:s') > $user->end_time) {
                                        $isAbsent = true;
                                    }
                                }
                                
                                if ($isAbsent) {
                                    return [
                                        'name' => $user->name,
                                        'clock_in' => '-',
                                        'status' => 'Tidak Hadir',
                                        'category' => 'absent',
                                        'color_class' => 'text-gray-800 bg-gray-200'
                                    ];
                                }
                                return null;
                            })->filter()->values();

                            $jsData = $jsData->merge($absentData);
                        @endphp
                        <div onclick="showDetails('{{ $currentDate }}', {{ json_encode($jsData) }})" 
                             class="h-16 bg-white border {{ $isToday ? 'border-blue-500 ring-1 ring-blue-500' : 'border-gray-200' }} rounded p-1 hover:shadow-md transition-shadow relative overflow-hidden cursor-pointer group">
                            <div class="font-bold {{ $isToday ? 'text-blue-600' : 'text-gray-700' }} text-xs mb-1">{{ $day }}</div>
                            
                            <div class="flex flex-wrap gap-0.5 content-start">
                                @foreach($daysAttendances as $att)
                                    @php
                                        $color = 'bg-green-500';
                                        if ($att->user->start_time && $att->clock_in) {
                                            $attendanceDate = \Carbon\Carbon::parse($att->date)->format('Y-m-d');
                                            $startTime = \Carbon\Carbon::parse($attendanceDate.' '.$att->user->start_time);
                                            $clockIn = \Carbon\Carbon::parse($attendanceDate.' '.$att->clock_in);
                                            $diffInSeconds = $startTime->diffInSeconds($clockIn, false);
                                            $lateMinutes = max(0, intdiv($diffInSeconds, 60));
                                        if ($lateMinutes > 0 && $lateMinutes <= 30) {
                                                $color = 'bg-yellow-400';
                                        } elseif ($lateMinutes > 30) {
                                                $color = 'bg-red-500';
                                            }
                                        }
                                    @endphp
                                    <div class="w-2 h-2 rounded-full {{ $color }}"></div>
                                @endforeach
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Detail Kehadiran</h3>
                        <p class="text-sm text-gray-500 mb-4" id="modal-date"></p>
                        
                        <div class="space-y-4">
                            <!-- Late > 30m -->
                            <div>
                                <h4 class="text-sm font-bold text-red-600 mb-2 border-b border-red-200 pb-1">Telat > 30 Menit</h4>
                                <ul id="list-late-over-30" class="space-y-2 text-sm"></ul>
                                <p id="empty-late-over-30" class="text-xs text-gray-400 italic hidden">Tidak ada data</p>
                            </div>
                            
                            <!-- Late <= 30m -->
                            <div>
                                <h4 class="text-sm font-bold text-yellow-600 mb-2 border-b border-yellow-200 pb-1">Telat &le; 30 Menit</h4>
                                <ul id="list-late-under-30" class="space-y-2 text-sm"></ul>
                                <p id="empty-late-under-30" class="text-xs text-gray-400 italic hidden">Tidak ada data</p>
                            </div>
                            
                            <!-- On Time -->
                            <div>
                                <h4 class="text-sm font-bold text-green-600 mb-2 border-b border-green-200 pb-1">Tepat Waktu</h4>
                                <ul id="list-ontime" class="space-y-2 text-sm"></ul>
                                <p id="empty-ontime" class="text-xs text-gray-400 italic hidden">Tidak ada data</p>
                            </div>

                            <!-- Absent -->
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2 border-b border-gray-200 pb-1">Tidak Hadir</h4>
                                <ul id="list-absent" class="space-y-2 text-sm"></ul>
                                <p id="empty-absent" class="text-xs text-gray-400 italic hidden">Tidak ada data</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetails(date, data) {
        // Format Date
        const dateObj = new Date(date);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('modal-date').textContent = dateObj.toLocaleDateString('id-ID', options);
        
        // Clear Lists
        const lists = ['late-over-30', 'late-under-30', 'ontime', 'absent'];
        lists.forEach(id => {
            document.getElementById(`list-${id}`).innerHTML = '';
            document.getElementById(`empty-${id}`).classList.remove('hidden');
        });

        // Populate Lists
        if (data && data.length > 0) {
            data.forEach(item => {
                let listId = 'ontime';
                if (item.category === 'late_over_30') listId = 'late-over-30';
                if (item.category === 'late_under_30') listId = 'late-under-30';
                if (item.category === 'absent') listId = 'absent';
                
                const ul = document.getElementById(`list-${listId}`);
                const li = document.createElement('li');
                li.className = `flex justify-between items-center p-2 rounded ${item.color_class}`;
                li.innerHTML = `
                    <span class="font-medium">${item.name}</span>
                    <span class="text-xs font-mono">${item.status}</span>
                `;
                ul.appendChild(li);
                
                document.getElementById(`empty-${listId}`).classList.add('hidden');
            });
        }
        
        // Show Modal
        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }
</script>
@endsection

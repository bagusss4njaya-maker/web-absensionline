@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Jadwal Kerja</h2>
    </div>

    <!-- Global Schedule Setting -->
    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Atur Jadwal Semua Pegawai</h3>
        <p class="text-gray-600 mb-4 text-sm">Pengaturan ini akan mengubah jam masuk dan pulang untuk <strong>seluruh pegawai</strong> sekaligus.</p>
        
        <form action="{{ route('admin.schedules.bulk-update') }}" method="POST" class="flex flex-col md:flex-row items-end gap-4">
            @csrf
            @method('PUT')
            
            <div class="w-full md:w-1/3">
                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Jadwal Masuk</label>
                <input type="time" name="start_time" id="start_time" value="09:00:00" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" required>
            </div>

            <div class="w-full md:w-1/3">
                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Jadwal Pulang</label>
                <input type="time" name="end_time" id="end_time" value="17:00:00" step="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" required>
            </div>

            <div class="w-full md:w-auto">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-sm font-medium transition-colors" onclick="return confirm('Apakah Anda yakin ingin mengubah jadwal untuk SEMUA pegawai?');">
                    Terapkan ke Semua
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                            {{ $user->start_time }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">
                            {{ $user->end_time }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Belum ada data pegawai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

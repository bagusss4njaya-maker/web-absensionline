<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function clockIn()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            return back()->with('error', 'Anda sudah absen masuk hari ini.');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'clock_in' => now()->toTimeString(),
        ]);

        return back()->with('success', 'Berhasil absen masuk!');
    }

    public function clockOut()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum absen masuk hari ini.');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'Anda sudah absen pulang hari ini.');
        }

        // Cek apakah sudah waktunya pulang
        if (now()->format('H:i:s') < $user->end_time) {
            return back()->with('error', 'Belum waktunya pulang. Jadwal pulang Anda pukul ' . $user->end_time);
        }

        $attendance->update([
            'clock_out' => now()->toTimeString(),
        ]);

        return back()->with('success', 'Berhasil absen pulang!');
    }
}

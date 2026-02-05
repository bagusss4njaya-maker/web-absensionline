<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        $users = User::where('role', 'user')->get();
        $totalUsers = $users->count();
        $today = now()->toDateString();
        $now = now()->format('H:i:s');
        
        $presentUserIds = Attendance::where('date', $today)->pluck('user_id')->toArray();
        $todayAttendance = count($presentUserIds);
        
        $lateAttendance = Attendance::where('date', $today)
            ->where('clock_in', '>', '09:00:00') // Assuming 9 AM is late
            ->count();
            
        // Calculate strict Absent (No attendance AND Time > End Time)
        $absentCount = 0;
        foreach ($users as $user) {
            if (!in_array($user->id, $presentUserIds)) {
                if ($now > $user->end_time) {
                    $absentCount++;
                }
            }
        }
            
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->orderBy('clock_in', 'desc')->get();
        
        return view('admin.dashboard', compact('attendances', 'totalUsers', 'todayAttendance', 'lateAttendance', 'absentCount'));
    }

    public function calendar()
    {
        $attendances = Attendance::with('user')->get();
        $users = User::where('role', 'user')->get();
        return view('admin.calendar', compact('attendances', 'users'));
    }

    public function user()
    {
        $user = Auth::user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->first();
        
        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('user.dashboard', compact('todayAttendance', 'history'));
    }
}

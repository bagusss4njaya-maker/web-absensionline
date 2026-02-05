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
        $totalUsers = User::where('role', 'user')->count();
        $todayAttendance = Attendance::where('date', now()->toDateString())->count();
        $lateAttendance = Attendance::where('date', now()->toDateString())
            ->where('clock_in', '>', '09:00:00') // Assuming 9 AM is late
            ->count();
            
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->orderBy('clock_in', 'desc')->get();
        
        return view('admin.dashboard', compact('attendances', 'totalUsers', 'todayAttendance', 'lateAttendance'));
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

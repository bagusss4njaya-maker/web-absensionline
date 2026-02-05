<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = User::where('role', 'user')->latest()->get();
        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = User::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        
        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }

        $employee->save();

        return redirect()->route('admin.employees.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = User::findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil dihapus.');
    }

    /**
     * Schedule Management Methods
     */
    public function scheduleIndex()
    {
        $users = User::where('role', 'user')->paginate(10);
        return view('admin.schedules.index', compact('users'));
    }

    public function scheduleEdit(User $user)
    {
        return view('admin.schedules.edit', compact('user'));
    }

    public function scheduleUpdate(Request $request, User $user)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ]);

        $user->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal kerja berhasil diperbarui.');
    }

    public function scheduleBulkUpdate(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ]);

        User::where('role', 'user')->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal semua pegawai berhasil diperbarui.');
    }

    /**
     * Attendance Management Methods
     */
    public function attendanceIndex(Request $request)
    {
        $query = Attendance::with('user')->latest('date');

        // Filter by Employee
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Date
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('date', $request->date);
        }

        $attendances = $query->paginate(10)->withQueryString();
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.attendances.index', compact('attendances', 'users'));
    }

    public function attendanceEdit(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('admin.attendances.edit', compact('attendance'));
    }

    public function attendanceUpdate(Request $request, string $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'clock_in' => 'required',
            'clock_out' => 'nullable',
        ]);

        $attendance->update([
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
        ]);

        return redirect()->route('admin.attendances.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function attendanceDestroy(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('admin.attendances.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}

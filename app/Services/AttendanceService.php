<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Exception;

class AttendanceService
{
    /**
     * Record employee clock-in with automated shift grace period & late calculation.
     */
    public function clockIn(Employee $employee, string $ipAddress, ?string $notes = null, ?Carbon $time = null): Attendance
    {
        $now = $time ?: Carbon::now();
        $today = $now->toDateString();

        $existing = Attendance::where('employee_id', $employee->id)->where('date', $today)->first();

        if ($existing && $existing->clock_in) {
            throw new Exception("Anda sudah melakukan Clock-In hari ini pada pukul {$existing->clock_in}.");
        }

        if ($existing && $existing->status === 'On Leave') {
            throw new Exception("Anda sedang dalam status cuti (On Leave) hari ini.");
        }

        $shift = $employee->shift;
        if (! $shift) {
            throw new Exception("Shift kerja belum ditetapkan untuk karyawan ini.");
        }

        $shiftStartTime = Carbon::parse($today . ' ' . $shift->start_time);
        $graceLimit = (clone $shiftStartTime)->addMinutes($shift->grace_period_minutes);
        $clockInDateTime = Carbon::parse($today . ' ' . $now->format('H:i:s'));

        $status = 'Present';
        $lateMinutes = 0;

        if ($clockInDateTime->greaterThan($graceLimit)) {
            $status = 'Late';
            $lateMinutes = (int) $shiftStartTime->diffInMinutes($clockInDateTime);
        }

        $clockInTime = $now->format('H:i:s');

        if ($existing) {
            $existing->update([
                'shift_id' => $shift->id,
                'clock_in' => $clockInTime,
                'late_minutes' => $lateMinutes,
                'status' => $status,
                'ip_address' => $ipAddress,
                'notes' => $notes ?: $existing->notes,
            ]);

            return $existing;
        }

        return Attendance::create([
            'employee_id' => $employee->id,
            'shift_id' => $shift->id,
            'date' => $today,
            'clock_in' => $clockInTime,
            'clock_out' => null,
            'late_minutes' => $lateMinutes,
            'status' => $status,
            'ip_address' => $ipAddress,
            'notes' => $notes,
        ]);
    }

    /**
     * Record employee clock-out.
     */
    public function clockOut(Employee $employee, ?Carbon $time = null): Attendance
    {
        $now = $time ?: Carbon::now();
        $today = $now->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)->where('date', $today)->first();

        if (! $attendance || ! $attendance->clock_in) {
            throw new Exception("Anda belum melakukan Clock-In hari ini.");
        }

        if ($attendance->clock_out) {
            throw new Exception("Anda sudah melakukan Clock-Out hari ini pada pukul {$attendance->clock_out}.");
        }

        $attendance->update([
            'clock_out' => $now->format('H:i:s'),
        ]);

        return $attendance;
    }

    /**
     * HR Manual Attendance Correction.
     */
    public function correctAttendance(Attendance $attendance, array $data): Attendance
    {
        $attendance->update([
            'clock_in' => $data['clock_in'] ?? $attendance->clock_in,
            'clock_out' => $data['clock_out'] ?? $attendance->clock_out,
            'late_minutes' => array_key_exists('late_minutes', $data) ? (int) $data['late_minutes'] : $attendance->late_minutes,
            'status' => $data['status'] ?? $attendance->status,
            'notes' => $data['notes'] ?? $attendance->notes,
        ]);

        return $attendance;
    }
}

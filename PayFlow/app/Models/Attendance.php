<?php
// app/Models/Attendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $primaryKey = 'attendance_id';
    protected $fillable = [
        'employee_id', 'date', 'time_in', 'time_out', 'total_hours', 'status', 'over_time'
    ];

    // Automatically compute total hours on save
    protected static function booted()
    {
        static::saving(function ($attendance) {
            if ($attendance->time_in && $attendance->time_out) {
                $in = Carbon::parse($attendance->time_in);
                $out = Carbon::parse($attendance->time_out);
                $attendance->total_hours = round($out->diffInMinutes($in) / 60, 2);
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

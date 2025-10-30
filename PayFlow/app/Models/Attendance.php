<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $primaryKey = 'attendance_id'; // matches migration

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'total_hours', 
        'status',
    ];

    // Relationship: Attendance belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}

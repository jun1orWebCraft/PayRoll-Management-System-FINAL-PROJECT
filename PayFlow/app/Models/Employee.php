<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // for login/auth
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;


class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'employee_id'; // specify primary key

    protected $fillable = [
        'employee_no',
        'QR_code',
        'first_name',
        'last_name',
        'email',
        'birthday',
        'age',
        'password',
        'phone',
        'address',
        'hire_date',
        'basic_salary',
        'status',
        'employment_type',
        'position_id',
        'profile_picture',
    ];
    public function getFullNameAttribute()
    {   
        return "{$this->first_name} {$this->last_name}";
    }


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'birthday' => 'date',
    ];

    // ------------------------
    // Relationships
    // ------------------------

    // Employee belongs to a Position
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    // Employee has many Attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }

    // Employee has many Payrolls
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employee_id', 'employee_id');
    }

    // Employee has many Deductions
    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'employee_id', 'employee_id');
    }

    // Employee has many Leave Requests (as requester)
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id', 'employee_id');
    }

    // Employee can approve Leave Requests (as approver)
    public function approvedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by', 'employee_id');
    }
    // In Employee.php (model)
    public static function generateEmployeeNo()
    {
        $year = now()->format('y');
        $latest = self::whereYear('created_at', now()->year)->latest('employee_id')->first();

        if ($latest && preg_match('/\d{2}-(\d+)/', $latest->employee_no, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }

        return $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }
  

}

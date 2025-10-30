<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'leave_request_id'; // matches migration

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
    ];

    // Relationship: LeaveRequest belongs to the Employee who requested
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Relationship: LeaveRequest belongs to the Employee/HR who approved
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'employee_id');
    }
}

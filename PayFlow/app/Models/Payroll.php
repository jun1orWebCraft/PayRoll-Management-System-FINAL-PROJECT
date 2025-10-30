<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $primaryKey = 'payroll_id'; // matches migration

    protected $fillable = [
        'employee_id',
        'pay_period_start',
        'pay_period_end',
        'basic_salary',
        'overtime_pay',
        'deductions',
        'net_pay',
        'payment_date',
        'status',
    ];

    // Relationship: Payroll belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Optional: If you want to get related deductions for this payroll
    public function deductions()
    {
        return $this->hasMany(Deduction::class, 'employee_id', 'employee_id');
    }

    // Optional: If you want to get related attendance for this payroll period
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id')
                    ->whereBetween('date', [$this->pay_period_start, $this->pay_period_end]);
    }
}

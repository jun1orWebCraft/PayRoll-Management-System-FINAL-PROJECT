<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $primaryKey = 'deduction_id'; // matches migration

    protected $fillable = [
        'employee_id',
        'deduction_name',
        'amount',
        'deduction_date',
        'remarks',
    ];

    // Relationship: Deduction belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}

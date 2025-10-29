<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $primaryKey = 'position_id'; // ✅ specify primary key

    protected $fillable = [
        'position_name',
        'salary_rate',
        'description',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id', 'position_id');
    }
}

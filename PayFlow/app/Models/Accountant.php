<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accountant extends Model
{
    use HasFactory;

    protected $table = 'accountants'; // matches your DB table name

    protected $primaryKey = 'accountant_id'; 
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'position',
        'contact_number',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation: an accountant can process many payrolls
    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'accountant_id');
    }
}

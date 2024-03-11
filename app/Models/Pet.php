<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vetAppointments()
    {
        return $this->hasMany(VetAppointment::class);
    }

    protected $fillable = [
        'name',
        'gender',
        'specie_id',
        'user_id',
    ];
}

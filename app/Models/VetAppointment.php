<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VetAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_date',
        'reason',
        'pet_id',
        'user_id'
    ];
}

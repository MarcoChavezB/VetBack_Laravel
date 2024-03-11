<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VetPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnosis',
        'observations',
        'indications',
        'vet_id',
        'vet_appointment_id'
    ];
}

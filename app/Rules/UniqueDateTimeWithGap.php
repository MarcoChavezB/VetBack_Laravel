<?php

namespace App\Rules;

use App\Models\VetAppointment;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class UniqueDateTimeWithGap implements Rule
{
    private $petId;
    private $gapMinutes;
    private $startTime;
    private $endTime;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($petId, $gapMinutes, $startTime, $endTime)
    {
        $this->petId = $petId;
        $this->gapMinutes = $gapMinutes;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }


    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $dateTime = Carbon::parse($value);

        if ($dateTime < $this->startTime || $dateTime > $this->endTime) {
            return false;
        }

        $existingAppointment = VetAppointment::where('pet_id', $this->petId)
            ->where('appointment_date', $dateTime->toDateString())
            ->where('appointment_time', '>=', $dateTime->toTimeString())
            ->where('appointment_time', '<', $dateTime->addMinutes($this->gapMinutes)->toTimeString())
            ->first();

        return !$existingAppointment;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'La fecha y hora seleccionada no está disponible. Debes elegir una fecha y hora dentro del rango de :start_time a :end_time, con una diferencia mínima de :minutes minutos.'. str_replace(':start_time', $this->startTime, str_replace(':end_time', $this->endTime, str_replace(':minutes', $this->gapMinutes, 'La fecha y hora seleccionada no está disponible. Debes elegir una fecha y hora dentro del rango de :start_time a :end_time, con una diferencia mínima de :minutes minutos.')));
    }
}

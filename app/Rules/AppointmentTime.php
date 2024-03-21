<?php

namespace App\Rules;

use App\Models\VetAppointment;
use Illuminate\Contracts\Validation\Rule;

class AppointmentTime implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $count = VetAppointment::where('appointment_date', '>=', $value)
            ->where('appointment_date', '<', date('Y-m-d H:i:s', strtotime($value . ' +1 hour')))
            ->count();

        // Si el conteo es mayor que 0, significa que ya existe una cita en esa hora
        return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ya existe una cita en esa hora.';;
    }
}

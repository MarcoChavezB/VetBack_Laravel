<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Logs extends Eloquent
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'id_usuario',
        'endpoint',
        'method',
        'data_sent', //los datos enviados
        'data_received', // los datos recibidos, en caso de get sera poner el query
        'date'
    ];
}

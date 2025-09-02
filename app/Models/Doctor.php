<?php

namespace App\Models;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}

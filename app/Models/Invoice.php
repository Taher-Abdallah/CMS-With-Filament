<?php

namespace App\Models;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Filament\Forms\Components\Select;
use App\Enums\AppointmentStatus;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\DatePicker;

class Invoice extends Model
{
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }



}

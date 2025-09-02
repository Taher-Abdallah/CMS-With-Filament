<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\TimePicker;

use Filament\Forms\Components\DatePicker;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $casts = [
        'date' => 'datetime',
        'appointment_status' => AppointmentStatus::class,
        'status' => \App\Enums\AppointmentStatusColor::class,
// 
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public static function getForm(): array
    {
        return [
            Select::make('patient_id')
                ->label('Patient ')
                ->required()
                ->searchable()
                ->preload()
                ->CreateOptionForm(Patient::getForm())
                ->relationship('patient', 'name')
                ->createOptionUsing(function (array $data) {
                    // هنا بيعمل create للموديل Patient
                    $patient = Patient::create($data);
                    // نخلي الـ select ياخد الـ id الجديد
                    return $patient->id;
                }) // بيخلي اللي انت عملتله create الاساسي

            ,
            Select::make('doctor_id')
                ->relationship('doctor', 'name')
                ->label('Doctor')
                ->getOptionLabelFromRecordUsing(fn($record) => $record->name . ' - ' . $record->specialty) // دي تعدلك الاسم وجمبه التخصص وتخليك قادر تبحث عن التخصص
                ->searchable()
                ->preload()
                ->required(),
            DatePicker::make('date')
                ->default(now())
                ->required()
                ->minDate(today())
                ,
            TimePicker::make('time')
                ->required()
                ->displayFormat('h:i A') // 12 ساعة للمستخدم
                ->format('H:i:s')
                ->minDate(now()),
            Select::make('status')
                ->options(
                    AppointmentStatus::class
                )
                ->required(),
        ];
    }
}

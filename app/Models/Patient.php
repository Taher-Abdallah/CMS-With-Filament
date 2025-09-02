<?php

namespace App\Models;

use App\Models\Medication;
use App\Models\Appointment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public static function getForm() :array
    {
        return [
            Section::make('Personal Details')->schema([
                            TextInput::make('name')
                ->required(),
            TextInput::make('age')
                ->numeric()
                ->default(null),
            Select::make('gender')->options([
                'male' => 'Male',
                'female' => 'Female',
            ]),
            TextInput::make('phone')
                ->tel()
                ->required(),
        ])->columns(2),


            Section::make('Address Information')->schema([
            Select::make('Country')
                ->label('Country')
                ->options(
                    Patient::query()
                        ->select('Country')
                        ->distinct()
                        ->orderBy('Country')
                        ->pluck('Country', 'Country')
                )
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn(callable $set) => [
                    $set('state', null), // نصفر الـ state
                    $set('City', null),  // نصفر الـ City كمان
                ]),
            Select::make('state')
                ->label('State')
                ->options(function (callable $get) {
                    if (! $get('Country')) {
                        return [];
                    }

                    return Patient::query()
                        ->where('Country', $get('Country'))
                        ->select('state')
                        ->distinct()
                        ->orderBy('state')
                        ->pluck('state', 'state');
                })
                ->searchable()
                ->preload()
                ->reactive()
                ->afterStateUpdated(fn(callable $set) => $set('City', null)), // لما تغير الـ state يمسح الـ City

            Select::make('City')
                ->label('City')
                ->options(function (callable $get) {
                    if (! $get('Country') || ! $get('state')) {
                        return [];
                    }

                    return Patient::query()
                        ->where('Country', $get('Country'))
                        ->where('state', $get('state'))
                        ->select('City')
                        ->distinct()
                        ->orderBy('City')
                        ->pluck('City', 'City');
                })
                ->searchable()
                ->preload()

            ])->columns(3),
        
        ];
    }
}

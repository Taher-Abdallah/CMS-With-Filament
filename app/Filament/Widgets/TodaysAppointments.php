<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TodaysAppointments extends BaseWidget
{
    protected int | string | array $columnSpan = 'full'; // ياخد عرض كبير فالداشبورد

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                fn(): Builder => Appointment::query()
                    ->whereDate('created_at', today())
                    ->orderBy('created_at', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable(),

                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('time')
                    ->label('Time')
                ->time('h:i A')
                    ,

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'confirmed',
                        'warning' => 'pending',
                        'danger'  => 'cancelled',
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Patients', Patient::count())
                ->description('32k increase')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Total Doctors', Doctor::count()),
            Stat::make('Total Appointments', Appointment::count()),
            Stat::make('Todays Appointments', Appointment::whereDate('date', today())->count()),
            Stat::make('This Month revenues', Invoice::whereMonth('created_at', now()->month)->sum('amount'))
                ->description('8.5% increase')
                ->color('success')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }
}

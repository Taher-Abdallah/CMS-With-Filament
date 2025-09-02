<?php

namespace App\Filament\Resources\PatientResource\Widgets;

use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PatientsCount extends BaseWidget
{

protected function getStats(): array
    {
        return [
            Stat::make('Total Patients', Patient::count()),
        ];
    }
}

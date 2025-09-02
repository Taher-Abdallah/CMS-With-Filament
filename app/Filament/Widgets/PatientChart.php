<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class PatientChart extends ChartWidget
{
    protected static ?string $heading = 'Patient Chart';
    public ?string $filter = 'year';
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        match ($activeFilter) {
            'today' => $data = Trend::model(Patient::class)
                ->between(
                    start: now()->startOfDay(),
                    end: now()->endOfDay(),
                )
                ->perHour()
                ->count(),
            'week' => $data = Trend::model(Patient::class)
                ->between(
                    start: now()->subDays(7)->startOfDay(),
                    end: now()->endOfDay(),
                )
                ->perDay()
                ->count(),
            'month' => $data = Trend::model(Patient::class)
                ->between(
                    start: now()->subDays(30)->startOfDay(),
                    end: now()->endOfDay(),
                )
                ->perDay()
                ->count(),
            '3 months' => $data = Trend::model(Patient::class)
                ->between(
                    start: now()->subDays(90)->startOfDay(),
                    end: now()->endOfDay(),
                )
                ->perWeek()
                ->count(),


            'year' => $data = Trend::model(Patient::class)
                ->between(
                    start: now()->startOfYear(),
                    end: now()->endOfYear(),
                )
                ->perMonth()
                ->count(),
        };

     

        return [
            'datasets' => [
                [
                    'label' => 'Patients',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}



<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Facades\DB;

class MonthlyRevnueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue';
    public ?string $filter = '3 months';
    protected function getData(): array
    {


        // استعلام يجيب الإيرادات مجمعة شهريًا
        $revenues = DB::table('invoices')
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // كل الشهور (1-12)
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // نحط صفر للشهور اللي مفيهاش داتا
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $revenues[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
    protected function getType(): string
    {
        return 'line';
    }
}
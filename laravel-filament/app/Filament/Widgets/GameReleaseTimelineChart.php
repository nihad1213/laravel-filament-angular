<?php

namespace App\Filament\Widgets;

use App\Models\Game;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GameReleaseTimelineChart extends ChartWidget
{
    protected static ?string $heading = 'Games by Release Year';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get games count by release year for the last 10 years
        $currentYear = Carbon::now()->year;
        $startYear = $currentYear - 9;

        $yearlyData = collect();
        
        for ($year = $startYear; $year <= $currentYear + 2; $year++) {
            $count = Game::whereYear('release_date', $year)->count();
            $yearlyData->push([
                'year' => $year,
                'count' => $count
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Games Released',
                    'data' => $yearlyData->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $yearlyData->pluck('year')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
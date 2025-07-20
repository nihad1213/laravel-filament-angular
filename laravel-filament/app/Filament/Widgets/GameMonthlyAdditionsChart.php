<?php

namespace App\Filament\Widgets;

use App\Models\Game;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class GameMonthlyAdditionsChart extends ChartWidget
{
    protected static ?string $heading = 'Games Added to Database (Last 12 Months)';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $monthlyData = collect();
        
        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Game::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
                
            $monthlyData->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Games Added',
                    'data' => $monthlyData->pluck('count')->toArray(),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                    ],
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $monthlyData->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
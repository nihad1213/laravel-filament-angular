<?php

namespace App\Filament\Widgets;

use App\Models\Platform;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PlatformsChart extends ChartWidget
{
    protected static ?string $heading = 'Games per Platform';

    protected function getData(): array
    {
        $platforms = Platform::withCount('games')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Games',
                    'data' => $platforms->pluck('games_count')->toArray(),
                    'backgroundColor' => [
                        '#4f46e5', '#ec4899', '#10b981', '#f59e0b', '#ef4444',
                    ],
                ],
            ],
            'labels' => $platforms->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

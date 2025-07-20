<?php

namespace App\Filament\Widgets;

use App\Models\Game;
use App\Models\Category;
use App\Models\Platform;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GameStatisticsChart extends ChartWidget
{
    protected static ?string $heading = 'Games by Category';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get games count by category
        $categoryData = DB::table('games')
            ->join('category_game', 'games.id', '=', 'category_game.game_id')
            ->join('categories', 'category_game.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(*) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Games per Category',
                    'data' => $categoryData->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF',
                        '#4BC0C0',
                        '#36A2EB',
                    ],
                    'borderColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40',
                        '#FF6384',
                        '#C9CBCF',
                        '#4BC0C0',
                        '#36A2EB',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $categoryData->pluck('name')->toArray(),
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
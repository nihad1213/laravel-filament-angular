<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class CategoriesChart extends ChartWidget
{
    protected static ?string $heading = 'Categories with Games';

    protected function getData(): array
    {
        $categories = Category::withCount('games')->get();

        return [
            'labels' => $categories->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Number of Games',
                    'data' => $categories->pluck('games_count')->toArray(),
                    'backgroundColor' => [
                        '#4f46e5',
                        '#ec4899',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#3b82f6',
                        '#8b5cf6',
                        '#22d3ee',
                        '#a3e635',
                        '#f97316',
                    ],
                ],
                
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

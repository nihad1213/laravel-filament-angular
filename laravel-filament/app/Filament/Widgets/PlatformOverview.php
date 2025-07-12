<?php

namespace App\Filament\Widgets;

use App\Models\Platform;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Platforms', Platform::query()->whereNotNull('name')->count()),
        ];
    }
}

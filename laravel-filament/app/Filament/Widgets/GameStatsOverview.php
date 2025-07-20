<?php

namespace App\Filament\Widgets;

use App\Models\Game;
use App\Models\Category;
use App\Models\Platform;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class GameStatsOverview extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        // Total Games
        $totalGames = Game::count();
        
        // Games added this month
        $gamesThisMonth = Game::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Games released this year
        $gamesThisYear = Game::whereYear('release_date', Carbon::now()->year)->count();
        
        // Total Categories
        $totalCategories = Category::count();
        
        // Total Platforms
        $totalPlatforms = Platform::count();
        
        // Total Admins
        $totalAdmins = User::count();
        
        // Recent games (last 7 days)
        $recentGames = Game::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        
        // Upcoming releases (future release dates)
        $upcomingGames = Game::where('release_date', '>', Carbon::now())->count();

        return [
            Stat::make('Total Games', $totalGames)
                ->description('All games in database')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('success'),
                
            Stat::make('Games This Month', $gamesThisMonth)
                ->description('Added in ' . Carbon::now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
                
            Stat::make('Released This Year', $gamesThisYear)
                ->description('Games released in ' . Carbon::now()->year)
                ->descriptionIcon('heroicon-m-rocket-launch')
                ->color('warning'),
                
            Stat::make('Recent Additions', $recentGames)
                ->description('Added in last 7 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary'),
                
            Stat::make('Upcoming Releases', $upcomingGames)
                ->description('Future release dates')
                ->descriptionIcon('heroicon-m-forward')
                ->color('gray'),
                
            Stat::make('Categories', $totalCategories)
                ->description('Total game categories')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),
                
            Stat::make('Platforms', $totalPlatforms)
                ->description('Total gaming platforms')
                ->descriptionIcon('heroicon-m-device-tablet')
                ->color('info'),
                
            Stat::make('Admins', $totalAdmins)
                ->description('Total administrators')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
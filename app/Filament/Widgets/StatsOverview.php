<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $userCount = User::count();

        $pendingTasks = Task::whereIn('status', ['todo', 'doing'])
            ->count();

        $finishedTasks = Task::whereIn('status', ['done'])
            ->count();


        return [
            Stat::make('Users', $userCount)
                ->description($userCount . ' ' . 'user verified')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
            Stat::make('Pending Task', $pendingTasks)
                ->description($pendingTasks . ' ' . 'task')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Finish Task', $finishedTasks)
                ->description($finishedTasks . ' ' . 'task')
                ->descriptionIcon('heroicon-m-check')
                ->color('success'),
        ];
    }
}

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
            Stat::make('Users', $userCount),
            Stat::make('Pending Task', $pendingTasks),
            Stat::make('Finish Task', $finishedTasks),
        ];
    }
}

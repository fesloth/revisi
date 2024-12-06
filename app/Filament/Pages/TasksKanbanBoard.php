<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Filament\Forms\Components\Toggle;

class TasksKanbanBoard extends KanbanBoard
{
    protected static ?string $title = 'Tasks';

    protected static string $headerView = 'tasks-kanban.kanban-header';

    protected static string $recordView = 'tasks-kanban.kanban-record';

    protected static string $statusView = 'tasks-kanban.kanban-status';

    protected static string $model = Task::class;

    protected static string $statusEnum = TaskStatus::class;

    protected function getEditModalFormSchema(null|int $recordId): array
    {
        return [
            // Select::make('task_user')
            //     ->relationship('users', 'name')
            //     ->required(),
            TextInput::make('title'),
            Textarea::make('description'),
            TextInput::make('progress')->numeric(),
            Toggle::make('urgent')
                ->onColor('warning')
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Task::class)
                ->form([
                    TextInput::make('title'),
                    Textarea::make('description'),
                    Toggle::make('urgent')
                        ->onColor('warning')
                ])
                ->mutateFormDataUsing(function ($data) {
                    $data['user_id'] = auth()->id();

                    return $data;
                })
        ];
    }
}

<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Models\Label;
use App\Models\Task;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
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

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string $model = Task::class;

    protected static string $statusEnum = TaskStatus::class;

    protected function getEditModalFormSchema(null|int $recordId): array
    {
        return [
            TextInput::make('title'),
            Select::make('labels')
                ->multiple()
                ->label('Label')
                ->options(Label::pluck('name', 'id')->toArray())
                ->createOptionForm([
                    TextInput::make('name')->required(),
                    ColorPicker::make('color')->required(),
                ])->createOptionUsing(function (array $data) {
                    return Label::create(array_merge($data, [
                        'user_id' => auth()->id(),
                    ]))->id;
                })
                ->required(),
            TextArea::make('description'),
            Toggle::make('urgent')
                ->onColor('warning'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Task::class)
                ->form([
                    TextInput::make('title'),
                    TextArea::make('description'),
                    Toggle::make('urgent')
                        ->onColor('warning')
                ])
                ->mutateFormDataUsing(function ($data) {
                    $data['user_id'] = auth()->id();

                    return $data;
                })
                ->after(function (CreateAction $action) {
                    $task = $action->getRecord();

                    $exists = $task->where('user_id', auth()->id())->exists();

                    if (!$exists) {
                        $task->taskUsers()->create([
                            'user_id' => auth()->id(),
                        ]);
                    }
                })
        ];
    }
}

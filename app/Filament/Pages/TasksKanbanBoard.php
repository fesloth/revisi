<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Models\Checklist;
use App\Models\ChecklistTask;
use App\Models\Label;
use App\Models\LabelUser;
use App\Models\Task;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
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
        $task = Task::find($recordId);

        return [
            Select::make('user_id')
                ->label('Member')
                ->options(User::pluck('name', 'id')->toArray())
                ->required(),
            // belum bisa assign karyawan
            // bikin fitur delete (hanya pemnbuat task yg bisa men-delete task)
            TextInput::make('title'),
            Select::make('labels')
                ->multiple()
                ->options(fn() => Label::pluck('name', 'id')->toArray())
                ->label('Label')
                ->preload()
                ->default(fn() => $task ? $task->labels()->pluck('id')->toArray() : [])
                ->afterStateHydrated(function (callable $set) use ($task) {
                    if ($task) {
                        $set('labels', $task->labels()->pluck('id')->toArray());
                    }
                }),
            TextArea::make('description'),
            Select::make('checklists')
                ->multiple()
                ->options(Checklist::pluck('name', 'id')->toArray())
                ->label('Checklists')
                ->default(fn() => $task ? $task->checklists()->pluck('id')->toArray() : [])
                ->createOptionForm([
                    TextInput::make('name')->required(),
                ])
                ->createOptionUsing(function (array $data) {
                    return Checklist::create([
                        'name' => $data['name'],
                        'user_id' => auth()->id(),
                        'is_done' => false,
                    ])->id;
                }),

            CheckboxList::make('checklist_tasks')
                ->label('')
                ->options(Checklist::pluck('name', 'id')->toArray())
                ->columns(2)
                ->default(fn() => $task
                    ? $task->checklists()->where('is_done', true)->pluck('id')->toArray()
                    : [])
                ->afterStateHydrated(function (callable $set) use ($task) {
                    if ($task) {
                        $set('checklist_tasks', $task->checklists()->where('is_done', true)->pluck('id')->toArray());
                    }
                }),
            Toggle::make('urgent')
                ->onColor('warning'),
        ];
    }

    protected function editRecord($recordId, array $data, array $state): void
    {
        $task = Task::find($recordId);


        if ($task) {
            logger('Syncing Labels: ', $data['labels'] ?? []);
            logger('Syncing Checklists: ', $data['checklists'] ?? []);

            $task->update($data);

            $task->labels()->sync($data['labels'] ?? []);
            $task->checklists()->sync($data['checklists'] ?? []);

            Checklist::whereIn('id', $data['checklists'] ?? [])->update(['is_done' => true]);
            Checklist::whereNotIn('id', $data['checklists'] ?? [])->update(['is_done' => false]);
        } else {
            logger('Task not found for ID: ' . $recordId);
        }
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

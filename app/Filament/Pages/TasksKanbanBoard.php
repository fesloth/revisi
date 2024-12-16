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
                ->multiple()
                ->label('Members')
                ->options(User::pluck('name', 'id')->toArray())
                ->afterStateHydrated(function (callable $set) use ($task) {
                    if ($task) {
                        $set('user_id', $task->users()->pluck('id')->toArray());
                    }
                })
                ->required(),
            // belum bisa assign karyawan
            // bikin fitur delete (hanya pemnbuat task yg bisa men-delete task)
            TextInput::make('title'),
            Select::make('labels')
                ->multiple()
                ->options(fn() => Label::pluck('name', 'id')->toArray())
                ->label('Label')
                ->createOptionForm([
                    TextInput::make('name')->required(),
                    ColorPicker::make('color')->required(),
                ])
                ->createOptionUsing(function (array $data) {
                    return Label::create(array_merge($data, [
                        'user_id' => auth()->id(),
                    ]))->id;
                })
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
                })
                ->afterStateHydrated(function (callable $set) use ($task) {
                    if ($task) {
                        $set('checklists', $task->checklists()->pluck('id')->toArray());
                    }
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
            logger('Syncing Users: ', $data['user_id'] ?? []);

            // Update task data
            $task->update($data);

            // Sync users (many-to-many relationship)
            $task->users()->sync($data['user_id'] ?? []);

            // Sync labels and checklists
            $task->labels()->sync($data['labels'] ?? []);
            $task->checklists()->sync($data['checklists'] ?? []);

            // Handle checklist tasks if provided
            if (isset($data['checklist_tasks'])) {
                Checklist::whereIn('id', $data['checklist_tasks'])->update(['is_done' => true]);
                $unselectedChecklists = $task->checklists->pluck('id')->diff($data['checklist_tasks']);
                Checklist::whereIn('id', $unselectedChecklists)->update(['is_done' => false]);
            }
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
                    Toggle::make('urgent')->onColor('warning'),
                ])
                ->mutateFormDataUsing(function ($data) {
                    $data['user_id'] = auth()->id();  // Only if you need to set a single user initially
                    return $data;
                })
                ->after(function (CreateAction $action) {
                    $task = $action->getRecord();
                    // If you want to assign multiple users, ensure they are synced to the pivot table
                    $task->users()->sync($data['user_id'] ?? []);  // Assuming 'user_id' is an array of user IDs
                })

        ];
    }
}

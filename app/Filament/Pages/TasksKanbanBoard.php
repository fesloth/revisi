<?php

namespace App\Filament\Pages;

use App\Enums\TaskStatus;
use App\Models\Checklist;
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
            // belum selesai membuat value label yang ada di database muncul
            Select::make('labels')
                ->multiple()
                ->options(fn(): array => Label::pluck('name', 'id')->toArray())
                ->label('Label')
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')->required(),
                    ColorPicker::make('color')->required(),
                ])
                ->createOptionUsing(function (array $data) {
                    return Label::create(array_merge($data, [
                        'user_id' => auth()->id(),
                    ]))->id;
                })
                ->default('label'),
            TextArea::make('description'),
            // mengubah select menjadi hanya input text dan membuat value nya tersimpan saat mengedit (sesuai task_id dan user_id)
            Select::make('checklists')
                ->multiple()
                ->options(Checklist::pluck('name', 'id')->toArray())
                ->label('Checklists')
                ->createOptionForm([
                    TextInput::make('name')->required(),
                ])
                ->createOptionUsing(function (array $data) {
                    return Checklist::create(array_merge($data, [
                        'user_id' => auth()->id(),
                        'is_done' => false,
                    ]))->id;
                }),
            CheckboxList::make('checklist_tasks')
                ->label('')
                ->options(Checklist::pluck('name', 'id')->toArray())
                ->columns(2)
                ->gridDirection('row')
                ->afterStateUpdated(function ($state) use ($task) {
                    // Ensure that all checklists are updated with the correct 'is_done' status
                    foreach (Checklist::all() as $checklist) {
                        $isDone = in_array($checklist->id, $state);
                        $checklist->update(['is_done' => $isDone]);

                        // Insert or update checklist task relation
                        if ($checklist->id) {
                            $task->checklistTasks()->updateOrCreate(
                                ['task_id' => $task->id, 'checklist_id' => $checklist->id],
                                ['is_done' => $isDone]
                            );
                        }
                    }
                }),


            Toggle::make('urgent')
                ->onColor('warning')
        ];
    }

    protected function editRecord($recordId, array $data, array $state): void
    {
        $task = Task::find($recordId);

        $task->update($data);
        $task->labels()->sync($data['labels']);
        $task->checklists()->sync($data['checklists']);
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

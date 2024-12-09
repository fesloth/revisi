<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Mokhosh\FilamentKanban\Concerns\HasRecentUpdateIndication;

class Task extends Model implements Sortable
{
    use HasFactory, SortableTrait, HasRecentUpdateIndication;

    protected $guarded = [];

    protected $fillable = ['title', 'description', 'urgent', 'label_id', 'user_id', 'status'];

    protected static function booted()
    {
        static::created(function ($task) {
            // Mengaitkan pengguna yang membuat task
            $task->users()->attach(auth()->id());

            // Mengaitkan label jika label_id ada
            if ($task->label_id) {
                $task->labels()->attach($task->label_id);
            }
        });
    }

    // user

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function taskUsers(): HasMany
    {
        return $this->hasMany(TaskUser::class);
    }

    // label

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_task', 'task_id', 'label_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    // checklist

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}

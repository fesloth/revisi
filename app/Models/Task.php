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

    protected $fillable = ['title', 'description', 'urgent', 'labels_id', 'user_id', 'status'];

    protected static function booted()
    {
        static::created(function ($task) {
            $task->users()->attach(auth()->id());
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function taskUsers(): HasMany
    {
        return $this->hasMany(TaskUser::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_task');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}

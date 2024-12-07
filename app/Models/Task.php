<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Mokhosh\FilamentKanban\Concerns\HasRecentUpdateIndication;

class Task extends Model implements Sortable
{
    use HasFactory, SortableTrait, HasRecentUpdateIndication;

    protected $guarded = [];

    protected $fillable = ['title', 'description', 'urgent', 'label', 'color', 'checklist', 'progress', 'user_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }

    public function team(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // public function owner(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'users');
    // }
}

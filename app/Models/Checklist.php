<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_done', 'user_id'];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user', 'label_id', 'label_task');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($checklist) {
            if (auth()->check()) {
                $checklist->user_id = auth()->id();
            }
        });
    }
}

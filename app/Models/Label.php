<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $table = 'labels';

    protected $fillable = ['name', 'color', 'user_id'];

    // public function tasks()
    // {
    //     return $this->belongsToMany(Task::class, 'task_user');
    // }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user', 'label_id', 'task_id', 'label_task');
    }

    public function labelUser()
    {
        return $this->hasMany(LabelUser::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($label) {
            if (auth()->check()) {
                $label->user_id = auth()->id();
            }
        });
    }
}

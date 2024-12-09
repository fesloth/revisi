<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $table = 'labels';

    protected $fillable = ['name', 'color', 'user_id'];

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function labelUser()
    {
        return $this->hasMany(LabelUser::class);
    }
}

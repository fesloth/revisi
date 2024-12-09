<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'task_user';

    protected $fillable = [
        'user_id',
        'task_id'
    ];
}

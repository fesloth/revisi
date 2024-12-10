<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistTask extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'checklist_tasks';

    protected $fillable = [
        'checklist_id',
        'task_id'
    ];
}

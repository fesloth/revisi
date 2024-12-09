<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelUser extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'label_task';

    protected $fillable = [
        'label_id',
        'task_id'
    ];
}

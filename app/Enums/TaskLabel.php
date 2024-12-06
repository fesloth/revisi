<?php

namespace App\Enums;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum TaskStatus: string
{
    use IsKanbanStatus;

    case Pending = 'Pending';
    case Important = 'Important';
    case Reviewing = 'Reviewing';
}

<?php

namespace App\Enum;

enum TaskStatus: string
{

    case Todo = 'to-do';
    case InProgress = 'in-progress';
    case Finished = 'finished';

}

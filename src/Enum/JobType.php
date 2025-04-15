<?php

namespace App\Enum;

enum JobType: string
{
    case FULL_TIME = 'Full-time';
    case PART_TIME = 'Part-time';
    case CONTRACT = 'Contract';
    case TEMPORARY = 'Temporary';
}
<?php

namespace App\Enum;

enum TransactionStatus: int
{
    case Pending = 1;
    case Completed = 2;
    case Failed = 3;
}

<?php

namespace App\Enum;

enum TransactionStatus: int
{
    case Create = 0;
    case Pending = 1;
    case Completed = 2;
    case Failed = 3;
    case RefundPending = 4;
    case RefundCompleted = 5;
}

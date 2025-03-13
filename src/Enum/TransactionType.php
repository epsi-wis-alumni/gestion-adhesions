<?php

namespace App\Enum;

enum TransactionType: int
{
    case Subscription = 1;
    case Donation = 2;
}

<?php

namespace App\Enum;

enum MembershipStatus: int
{
    case Pending = 1;
    case Approved = 2;
    case Rejected = 3;
}

<?php

namespace App\Enum;

enum MemberType: int
{
    case Undefined = 0;
    case Student = 1;
    case Alumni = 2;
    case Partner = 3;
}

<?php

namespace App\Enum;

enum JobOfferType: int
{
    case Undefined = 0;
    case Internship = 1;
    case Apprenticeship = 2;
    case CDD = 3;
    case CDI = 4;
}

<?php

namespace App\Enum;

enum JobOfferType: int
{
    case Undefined = 0;
    case Internship = 1;
    case Apprenticeship = 2;
    case CDD = 3;
    case CDI = 4;
    case Freelance = 5;

    public static function getChoices(): array
    {
        return [
            'Stage' => self::Internship,
            'Alternance' => self::Apprenticeship,
            'CDD' => self::CDD,
            'CDI' => self::CDI,
            'Freelance' => self::Freelance,
        ];
    }
}

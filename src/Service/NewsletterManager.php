<?php

namespace App\Service;

use App\Entity\MailTemplate;
use Symfony\Component\Finder\Finder;


final class NewsletterManager
{
    public function findMailsTemplates(iterable $mailTemplates): array
    {
        $templates = [];

        foreach ($mailTemplates as $mailTemplate) {
            $templates[$mailTemplate->getLabel()] = $mailTemplate;
        }

        return $templates;
    }
}

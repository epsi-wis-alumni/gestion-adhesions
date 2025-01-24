<?php

namespace App\Service;

use Symfony\Component\Finder\Finder;


final class NewsletterManager
{
    public function findMailsTemplates(): array
    {
        $templates = [];
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../templates/mails');

        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();
            $pattern = '/^(.*?)(?=\.html\.twig$)/';

            if (preg_match($pattern, $fileNameWithExtension, $matches)) {
                $filenameWithoutExtension = $matches[1];
            } else {
                $filenameWithoutExtension = "";
            }

            $templates[$filenameWithoutExtension] = $fileNameWithExtension;
        }

        return $templates;
    }
}

<?php

namespace App\Service;

use App\Entity\MailTemplate;
use App\Repository\MailTemplateRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MailTemplateManager
{
    public function __construct(
        protected ParameterBagInterface $parameterBag,
        protected MailTemplateRepository $mailTemplateRepository,
    ) {
    }

    /**
     * @return SplFileInfo[]
     */
    public function getTemplateFiles(): array
    {
        $templatesDirectory = $this->parameterBag->get('mails_base_path');
        $finder = Finder::create()->files()->name('*.html.twig')->in($templatesDirectory);

        return iterator_to_array($finder->getIterator());
    }

    public function getUnusedTemplateFiles(): array
    {
        $mailTemplates = $this->mailTemplateRepository->findAll();
        $templateFileNames = $this->getTemplateFiles();

        return array_diff(
            array_map(fn (SplFileInfo $file) => $file->getBasename(), $templateFileNames),
            array_map(fn (MailTemplate $mailTemplate): ?string => $mailTemplate->getFileName(), $mailTemplates),
        );
    }
}

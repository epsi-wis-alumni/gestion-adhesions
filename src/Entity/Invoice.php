<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Invoice
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePath = null;

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
}

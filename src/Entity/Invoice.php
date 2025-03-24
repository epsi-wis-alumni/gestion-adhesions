<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Validator\Constraints as Assert;

#[Embeddable]
class Invoice
{
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePath = null;
    
    #[Assert\NotBlank()]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $id = null;

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setInvoiceId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getInvoiceId(): ?string
    {
        return $this->id;
    }
}

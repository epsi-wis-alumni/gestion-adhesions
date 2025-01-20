<?php

namespace App\Entity\Trait;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeletedTrait
{
    #[ORM\Column(length: 255, nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?self $deletedBy = null;

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getDeletedBy(): ?self
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?self $deletedBy): static
    {
        $this->deletedBy = $deletedBy;

        return $this;
    } 
}





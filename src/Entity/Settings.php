<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping as ORM;

#[Embeddable]
class Settings
{
    #[ORM\Column]
    private ?bool $allowNewsletters = null;

    #[ORM\Column]
    private ?bool $allowNotifications = null;

    public function __construct()
    {
        $this->setAllowNewsletters(false);
        $this->setAllowNotifications(false);
    }

    public function isAllowNewsletters(): ?bool
    {
        return $this->allowNewsletters;
    }

    public function setAllowNewsletters(bool $allowNewsletters): static
    {
        $this->allowNewsletters = $allowNewsletters;

        return $this;
    }

    public function isAllowNotifications(): ?bool
    {
        return $this->allowNotifications;
    }

    public function setAllowNotifications(bool $allowNotifications): static
    {
        $this->allowNotifications = $allowNotifications;

        return $this;
    }
}

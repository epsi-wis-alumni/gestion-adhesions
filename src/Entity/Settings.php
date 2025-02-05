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

    #[ORM\Column]
    private ?bool $allowElectionNotifications = null;

    public function __construct()
    {
        $this->setAllowNewsletters(false);
        $this->setAllowNotifications(false);
        $this->setAllowElectionNotifications(false);
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

    public function isAllowElectionNotifications(): ?bool
    {
        return $this->allowElectionNotifications;
    }

    public function setAllowElectionNotifications(bool $allowElectionNotifications): static
    {
        $this->allowElectionNotifications = $allowElectionNotifications;

        return $this;
    }
}

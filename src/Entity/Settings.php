<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping as ORM;

#[Embeddable]
class Settings
{
    #[ORM\Column]
    private ?bool $newsletterAllowed = null;

    #[ORM\Column]
    private ?bool $notificationsAllowed = null;

    #[ORM\Column]
    private ?bool $electionNotificationsAllowed = null;

    public function __construct()
    {
        $this->setNewsletterAllowed(false);
        $this->setNotificationsAllowed(false);
    }

    public function isNotificationsAllowed(): ?bool
    {
        return $this->notificationsAllowed;
    }

    public function setNotificationsAllowed(bool $notificationsAllowed): static
    {
        $this->notificationsAllowed = $notificationsAllowed;

        return $this;
    }

    public function isElectionNotificationsAllowed(): ?bool
    {
        return $this->electionNotificationsAllowed;
    }

    public function setElectionNotificationsAllowed(bool $electionNotificationsAllowed): static
    {
        $this->electionNotificationsAllowed = $electionNotificationsAllowed;

        return $this;
    }

    public function isEventNotificationsAllowed(): ?bool
    {
        return $this->eventNotificationsAllowed;
    }

    public function setEventNotificationsAllowed(bool $eventNotificationsAllowed): static
    {
        $this->eventNotificationsAllowed = $eventNotificationsAllowed;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;

#[Embeddable]
class Settings
{
    #[ORM\Column]
    private ?bool $newsletterAllowed = null;

    #[ORM\Column]
    private ?bool $notificationsAllowed = null;

    #[ORM\Column]
    private ?bool $electionNotificationsAllowed = null;

    #[ORM\Column]
    private ?bool $eventNotificationsAllowed = null;

    public function __construct()
    {
        $this->setNewsletterAllowed(false);
        $this->setNotificationsAllowed(false);
        $this->setElectionNotificationsAllowed(false);
        $this->setEventNotificationsAllowed(false);
    }

    public function isNewsletterAllowed(): ?bool
    {
        return $this->newsletterAllowed;
    }

    public function setNewsletterAllowed(bool $newsletterAllowed): static
    {
        $this->newsletterAllowed = $newsletterAllowed;

        return $this;
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

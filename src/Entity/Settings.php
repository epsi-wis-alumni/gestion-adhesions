<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $allowNewsletters = null;

    #[ORM\Column]
    private ?bool $allowNotifications = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
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

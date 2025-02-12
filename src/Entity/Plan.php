<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\DecimalType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Subscription>
     */
    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'plan')]
    private Collection $subscription;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $yearly = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $monthly = null;

    #[ORM\Column]
    private ?bool $highlighted = null;

    public function __construct()
    {
        $this->subscription = new ArrayCollection();
        $this->highlighted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscription(): Collection
    {
        return $this->subscription;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscription->contains($subscription)) {
            $this->subscription->add($subscription);
            $subscription->setPlan($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscription->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getPlan() === $this) {
                $subscription->setPlan(null);
            }
        }

        return $this;
    }

    public function getYearly(): ?string
    {
        return $this->yearly;
    }

    public function setYearly(string $yearly): static
    {
        $this->yearly = $yearly;

        return $this;
    }

    public function getMonthly(): ?string
    {
        return $this->monthly;
    }

    public function setMonthly(string $monthly): static
    {
        $this->monthly = $monthly;

        return $this;
    }

    public function isHighlighted(): ?bool
    {
        return $this->highlighted;
    }

    public function setHighlighted(bool $highlighted): static
    {
        $this->highlighted = $highlighted;

        return $this;
    }
}

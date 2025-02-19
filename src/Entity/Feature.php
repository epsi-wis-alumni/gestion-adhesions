<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
class Feature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Plan::class, mappedBy: 'features')]
    private Collection $plans;

    
    public function __construct()
    {
        $this->plans = new ArrayCollection();
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

    public function getPlan(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): void
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
            $plan->addFeature($this);
        }
    }

    public function removePlan(Plan $plan): void
    {
        if ($this->plans->contains($plan)) {
            $this->plans->removeElement($plan);
            $plan->removeFeature($this);
        }
    }
}

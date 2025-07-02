<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Repository\ElectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ElectionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Election
{
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $jobTitle = null;

    #[Assert\DateTime()]
    #[ORM\Column]
    private ?\DateTimeImmutable $voteStartAt = null;

    #[Assert\DateTime()]
    #[Assert\GreaterThan(propertyPath: 'voteStartAt')]
    #[ORM\Column]
    private ?\DateTimeImmutable $voteEndAt = null;

    #[ORM\ManyToOne(inversedBy: 'createdElections')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'updatedElections')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    /**
     * @var Collection<int, Candidacy>
     */
    #[ORM\OneToMany(targetEntity: Candidacy::class, mappedBy: 'election', cascade: ['persist', 'remove'])]
    private Collection $candidacies;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'election', cascade: ['persist', 'remove'])]
    private Collection $votes;

    public function __construct()
    {
        $this->candidacies = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(string $jobTitle): static
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getVoteStartAt(): ?\DateTimeImmutable
    {
        return $this->voteStartAt;
    }

    public function setVoteStartAt(\DateTimeImmutable $voteStartAt): static
    {
        $this->voteStartAt = $voteStartAt;

        return $this;
    }

    public function getVoteEndAt(): ?\DateTimeImmutable
    {
        return $this->voteEndAt;
    }

    public function setVoteEndAt(\DateTimeImmutable $voteEndAt): static
    {
        $this->voteEndAt = $voteEndAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection<int, Candidacy>
     */
    public function getCandidacies(): Collection
    {
        return $this->candidacies;
    }

    public function addCandidacy(Candidacy $candidacy): static
    {
        if (!$this->candidacies->contains($candidacy)) {
            $this->candidacies->add($candidacy);
            $candidacy->setElection($this);
        }

        return $this;
    }

    public function removeCandidacy(Candidacy $candidacy): static
    {
        // set the owning side to null (unless already changed)
        if ($this->candidacies->removeElement($candidacy) && $candidacy->getElection() === $this) {
            $candidacy->setElection(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setElection($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        // set the owning side to null (unless already changed)
        if ($this->votes->removeElement($vote) && $vote->getElection() === $this) {
            $vote->setElection(null);
        }

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->voteEndAt < new \DateTimeImmutable();
    }

    public function isPending(): bool
    {
        return $this->voteStartAt > new \DateTimeImmutable();
    }
}

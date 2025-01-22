<?php

namespace App\Entity;

use App\Repository\ElectionRepository;
use App\Repository\VoteRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElectionRepository::class)]
class Election
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $jobTitle = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $voteStartAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $voteEndAt = null;

    #[ORM\ManyToOne(inversedBy: 'elections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    /**
     * @var Collection<int, Candidate>
     */
    #[ORM\OneToMany(targetEntity: Candidate::class, mappedBy: 'election')]
    private Collection $candidates;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'election')]
    private Collection $votes;

    public function __construct()
    {
        $this->candidates = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->setCreatedAt(new DateTimeImmutable());
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    /**
     * @return Collection<int, Candidate>
     */
    public function getCandidates(): Collection
    {
        return $this->candidates;
    }

    public function addCandidate(Candidate $candidate): static
    {
        if (!$this->candidates->contains($candidate)) {
            $this->candidates->add($candidate);
            $candidate->setElection($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): static
    {
        if ($this->candidates->removeElement($candidate)) {
            // set the owning side to null (unless already changed)
            if ($candidate->getElection() === $this) {
                $candidate->setElection(null);
            }
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
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getElection() === $this) {
                $vote->setElection(null);
            }
        }

        return $this;
    }

    public function getResult($candidates, int $electionId, VoteRepository $voteRepository): array
    {
        $result = [];
        foreach ($candidates as $candidate) {
            $userId = $candidate->getCandidate()->getId();
            $result[$userId] = $voteRepository->getNbVote($userId, $electionId);
        }
        return $result;
    }
}

<?php

namespace App\Entity;

use App\Entity\Trait\SoftDeletedTrait;
use App\Enum\MembershipStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface
{
    use SoftDeletedTrait;
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_APPROVED = 'ROLE_APPROVED';
    public const ROLE_MEMBER = 'ROLE_MEMBER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jobTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $microsoftId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $githubId = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'user')]
    private Collection $transactions;

    /**
     * @var Collection<int, Election>
     */
    #[ORM\OneToMany(targetEntity: Election::class, mappedBy: 'createdBy')]
    private Collection $elections;

    /**
     * @var Collection<int, Candidate>
     */
    #[ORM\OneToMany(targetEntity: Candidate::class, mappedBy: 'candidate')]
    private Collection $candidates;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'voter')]
    private Collection $votes;

    /**
     * @var Collection<int, Newsletter>
     */
    #[ORM\OneToMany(targetEntity: Newsletter::class, mappedBy: 'createdBy')]
    private Collection $createdNewsletters;

    /**
     * @var Collection<int, Newsletter>
     */
    #[ORM\OneToMany(targetEntity: Newsletter::class, mappedBy: 'sentBy')]
    private Collection $sentNewsletters;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'approvedUsers')]
    private ?self $approvedBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'approvedBy')]
    private Collection $approvedUsers;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $approvedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $rejectedAt = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'rejectedUsers')]
    private ?self $rejectedBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'rejectedBy')]
    private Collection $rejectedUsers;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->elections = new ArrayCollection();
        $this->candidates = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->createdNewsletters = new ArrayCollection();
        $this->sentNewsletters = new ArrayCollection();
        $this->approvedUsers = new ArrayCollection();
        $this->rejectedUsers = new ArrayCollection();
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response, string $resourceOwnerName): UserInterface
    {
        $this->setEmail($response->getEmail());
        $this->setFirstname($response->getFirstName());
        $this->setLastname($response->getLastName());
        $this->setAvatar($response->getProfilePicture());

        match ($resourceOwnerName) {
            'google' => $this->setGoogleId($response->getUserIdentifier()),
            'azure' => $this->setMicrosoftId($response->getUserIdentifier()),
            'github' => $this->setGithubId($response->getUserIdentifier()),
        };

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = array_unique($roles);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDisplayName(bool $reverse = false): string
    {
        $names = [strtoupper($this->getLastname()), $this->getFirstname()];

        if ($reverse) {
            $names = array_reverse($names);
        }

        return join(' ', $names);
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): static
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getMicrosoftId(): ?string
    {
        return $this->microsoftId;
    }

    public function setMicrosoftId(?string $microsoftId): static
    {
        $this->microsoftId = $microsoftId;

        return $this;
    }

    public function getGithubId(): ?string
    {
        return $this->githubId;
    }

    public function setGithubId(?string $githubId): static
    {
        $this->githubId = $githubId;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Election>
     */
    public function getElections(): Collection
    {
        return $this->elections;
    }

    public function addElection(Election $election): static
    {
        if (!$this->elections->contains($election)) {
            $this->elections->add($election);
            $election->setUser($this);
        }

        return $this;
    }

    public function removeElection(Election $election): static
    {
        if ($this->elections->removeElement($election)) {
            // set the owning side to null (unless already changed)
            if ($election->getUser() === $this) {
                $election->setUser(null);
            }
        }

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
            $candidate->setUser($this);
        }

        return $this;
    }

    public function removeCandidate(Candidate $candidate): static
    {
        if ($this->candidates->removeElement($candidate)) {
            // set the owning side to null (unless already changed)
            if ($candidate->getUser() === $this) {
                $candidate->setUser(null);
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
            $vote->setVoter($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getVoter() === $this) {
                $vote->setVoter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Newsletter>
     */
    public function getCreatedNewsletters(): Collection
    {
        return $this->createdNewsletters;
    }

    public function addCreatedNewsletter(Newsletter $newsletter): static
    {
        if (!$this->createdNewsletters->contains($newsletter)) {
            $this->createdNewsletters->add($newsletter);
            $newsletter->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedNewsletter(Newsletter $newsletter): static
    {
        if ($this->createdNewsletters->removeElement($newsletter)) {
            // set the owning side to null (unless already changed)
            if ($newsletter->getCreatedBy() === $this) {
                $newsletter->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Newsletter>
     */
    public function getSentNewsletters(): Collection
    {
        return $this->sentNewsletters;
    }

    public function addSentNewsletter(Newsletter $newsletter): static
    {
        if (!$this->sentNewsletters->contains($newsletter)) {
            $this->sentNewsletters->add($newsletter);
            $newsletter->setSentBy($this);
        }

        return $this;
    }

    public function removeSentNewsletter(Newsletter $newsletter): static
    {
        if ($this->sentNewsletters->removeElement($newsletter)) {
            // set the owning side to null (unless already changed)
            if ($newsletter->getSentBy() === $this) {
                $newsletter->setSentBy(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

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

    public function getApprovedBy(): ?self
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?self $approvedBy): static
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getApprovedUsers(): Collection
    {
        return $this->approvedUsers;
    }

    public function addApprovedUser(self $approvedUser): static
    {
        if (!$this->approvedUsers->contains($approvedUser)) {
            $this->approvedUsers->add($approvedUser);
            $approvedUser->setApprovedBy($this);
        }

        return $this;
    }

    public function removeApprovedUser(self $approvedUser): static
    {
        if ($this->approvedUsers->removeElement($approvedUser)) {
            // set the owning side to null (unless already changed)
            if ($approvedUser->getApprovedBy() === $this) {
                $approvedUser->setApprovedBy(null);
            }
        }

        return $this;
    }

    public function getApprovedAt(): ?\DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?\DateTimeImmutable $approvedAt): static
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    public function getRejectedAt(): ?\DateTimeImmutable
    {
        return $this->rejectedAt;
    }

    public function setRejectedAt(?\DateTimeImmutable $rejectedAt): static
    {
        $this->rejectedAt = $rejectedAt;

        return $this;
    }

    public function getRejectedBy(): ?self
    {
        return $this->rejectedBy;
    }

    public function setRejectedBy(?self $rejectedBy): static
    {
        $this->rejectedBy = $rejectedBy;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getRejectedUsers(): Collection
    {
        return $this->rejectedUsers;
    }

    public function addRejectedUser(self $rejectedUser): static
    {
        if (!$this->rejectedUsers->contains($rejectedUser)) {
            $this->rejectedUsers->add($rejectedUser);
            $rejectedUser->setRejectedBy($this);
        }

        return $this;
    }

    public function removeRejectedUser(self $rejectedUser): static
    {
        if ($this->rejectedUsers->removeElement($rejectedUser)) {
            // set the owning side to null (unless already changed)
            if ($rejectedUser->getRejectedBy() === $this) {
                $rejectedUser->setRejectedBy(null);
            }
        }

        return $this;
    }

    public function getStatus(): MembershipStatus
    {
        if ($this->getRejectedAt()) {
            return MembershipStatus::Rejected;
        }
        if ($this->getApprovedAt()) {
            return MembershipStatus::Approved;
        }

        return MembershipStatus::Pending;
    }

    public function getStatusColor(): string
    {
        return match ($this->getStatus()) {
            MembershipStatus::Approved => 'success',
            MembershipStatus::Rejected => 'danger',
            MembershipStatus::Pending => 'warning',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->getStatus()) {
            MembershipStatus::Approved => 'Approuvé',
            MembershipStatus::Rejected => 'Rejeté',
            MembershipStatus::Pending => 'En attente',
        };
    }
    
    public function hasCompleteInfo():bool
    {
        return !!$this->getCompany() && !!$this->getJobTitle();
    } 
}

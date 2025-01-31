<?php

namespace App\Entity;

use App\Repository\NewsletterRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\BlobType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
class Newsletter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $object = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $body = null;

    #[ORM\Column(length: 255)]
    private ?string $cta = null;

    #[ORM\ManyToOne(inversedBy: 'createdNewsletters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'sentNewsletters')]
    private ?User $sentBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $sendAt = null;

    #[ORM\ManyToOne(inversedBy: 'newsletters')]
    private ?MailTemplate $template = null;

    /**
     * @var Collection<int, UserNewsletter>
     */
    #[ORM\OneToMany(targetEntity: UserNewsletter::class, mappedBy: 'newsletter')]
    private Collection $userNewsletters;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->userNewsletters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): static
    {
        $this->object = $object;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getCta(): ?string
    {
        return $this->cta;
    }

    public function setCta(string $cta): static
    {
        $this->cta = $cta;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSentBy(): ?User
    {
        return $this->sentBy;
    }

    public function setSentBy(?User $sentBy): static
    {
        $this->sentBy = $sentBy;

        return $this;
    }

    public function getSendAt(): ?\DateTimeImmutable
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeImmutable $sendAt): static
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getTemplate(): ?MailTemplate
    {
        return $this->template;
    }

    public function setTemplate(?MailTemplate $template): static
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return Collection<int, UserNewsletter>
     */
    public function getUserNewsletters(): Collection
    {
        return $this->userNewsletters;
    }

    public function addUserNewsletter(UserNewsletter $userNewsletter): static
    {
        if (!$this->userNewsletters->contains($userNewsletter)) {
            $this->userNewsletters->add($userNewsletter);
            $userNewsletter->setNewsletter($this);
        }

        return $this;
    }

    public function removeUserNewsletter(UserNewsletter $userNewsletter): static
    {
        if ($this->userNewsletters->removeElement($userNewsletter)) {
            // set the owning side to null (unless already changed)
            if ($userNewsletter->getNewsletter() === $this) {
                $userNewsletter->setNewsletter(null);
            }
        }

        return $this;
    }
}

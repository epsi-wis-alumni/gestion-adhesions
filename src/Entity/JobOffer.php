<?php

namespace App\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Enum\JobOfferStatus;
use App\Enum\JobOfferType;
use App\Repository\JobOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    use TimestampableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', options: ['default' => '[]'])]
    private array $types = [];

    #[ORM\Column(enumType: JobOfferStatus::class, options: ['default' => JobOfferStatus::Online->value])]
    private ?JobOfferStatus $status = JobOfferStatus::Online;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilePath = null;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'jobOffers')]
    private Collection $skills;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'jobOffers')]
    private Collection $categories;

    /**
     * @var Collection<int, JobQuestion>
     */
    #[ORM\OneToMany(targetEntity: JobQuestion::class, mappedBy: 'jobOffer', cascade: ['remove', 'persist'])]
    private Collection $jobQuestions;

    #[ORM\ManyToOne(inversedBy: 'jobOffers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column(length: 255)]
    private ?string $company = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 12)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    private ?int $requiredExperience = null;

    /**
     * @var Collection<int, Language>
     */
    #[ORM\ManyToMany(targetEntity: Language::class, mappedBy: 'jobOffers')]
    private Collection $languages;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->jobQuestions = new ArrayCollection();
        $this->languages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getTypes(): array
    {
        return array_map(fn($type) => JobOfferType::from($type), $this->types);
    }

    public function setTypes(array $types): self
    {
        $this->types = array_map(fn(JobOfferType $type) => $type->value, $types);
        return $this;
    }

    public function addType(JobOfferType $type): self
    {
        if (!in_array($type->value, $this->types, true)) {
            $this->types[] = $type->value;
        }
        return $this;
    }

    public function removeType(JobOfferType $type): self
    {
        $this->types = array_filter($this->types, fn($t) => $t !== $type->value);
        return $this;
    }

    public function getStatus(): ?JobOfferStatus
    {
        return $this->status;
    }

    public function setStatus(JobOfferStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getImageFilePath(): ?string
    {
        return $this->imageFilePath;
    }

    public function setImageFilePath(?string $imageFilePath): static
    {
        $this->imageFilePath = $imageFilePath;

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, JobQuestion>
     */
    public function getJobQuestions(): Collection
    {
        return $this->jobQuestions;
    }

    public function addJobQuestion(JobQuestion $jobQuestion): static
    {
        if (!$this->jobQuestions->contains($jobQuestion)) {
            $this->jobQuestions->add($jobQuestion);
            $jobQuestion->setJobOffer($this);
        }

        return $this;
    }

    public function removeJobQuestion(JobQuestion $jobQuestion): static
    {
        if ($this->jobQuestions->removeElement($jobQuestion)) {
            // set the owning side to null (unless already changed)
            if ($jobQuestion->getJobOffer() === $this) {
                $jobQuestion->setJobOffer(null);
            }
        }

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getRequiredExperience(): ?int
    {
        return $this->requiredExperience;
    }

    public function setRequiredExperience(?int $requiredExperience): static
    {
        $this->requiredExperience = $requiredExperience;

        return $this;
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): static
    {
        if (!$this->languages->contains($language)) {
            $this->languages->add($language);
            $language->addJobOffer($this);
        }

        return $this;
    }

    public function removeLanguage(Language $language): static
    {
        if ($this->languages->removeElement($language)) {
            $language->removeJobOffer($this);
        }

        return $this;
    }
}

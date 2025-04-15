<?php

namespace App\Entity;

use App\Enum\JobType;
use App\Enum\ExperienceLevel;
use App\Enum\Status;
use App\Repository\JobListingRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: JobListingRepository::class)]
class JobListing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ListingName = null;

    #[ORM\Column(length: 255)]
    private ?string $Title = null;

    #[ORM\Column(length: 255)]
    private ?string $Location = null;

    #[ORM\Column(type: 'string', enumType: JobType::class)]
    private ?JobType $JobType = null;

    #[ORM\Column(length: 255)]
    private ?string $SalaryRange = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column(type: 'string', enumType: ExperienceLevel::class)]
    private ?ExperienceLevel $ExperienceLevel = null;

    #[ORM\Column(type: 'string', enumType: Status::class)]
    private ?Status $Status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: Application::class, cascade: ['remove'])]
    private Collection $applications;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function getListingName(): ?string
    {
        return $this->ListingName;
    }

    public function setListingName(string $ListingName): static
    {
        $this->ListingName = $ListingName;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): static
    {
        $this->Title = $Title;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->Location;
    }

    public function setLocation(string $Location): static
    {
        $this->Location = $Location;

        return $this;
    }

    public function getJobType(): ?JobType
    {
        return $this->JobType;
    }

    public function setJobType(JobType $JobType): static
    {
        $this->JobType = $JobType;

        return $this;
    }

    public function getSalaryRange(): ?string
    {
        return $this->SalaryRange;
    }

    public function setSalaryRange(string $SalaryRange): static
    {
        $this->SalaryRange = $SalaryRange;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getExperienceLevel(): ?ExperienceLevel
    {
        return $this->ExperienceLevel;
    }

    public function setExperienceLevel(ExperienceLevel $ExperienceLevel): static
    {
        $this->ExperienceLevel = $ExperienceLevel;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->Status;
    }

    public function setStatus(Status $Status): static
    {
        $this->Status = $Status;

        return $this;
    }
}
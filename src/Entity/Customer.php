<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\CustomerStatus;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Customer
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $acquisitionDate = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isBusiness = false;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Operation $operation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    /**
     * @var Collection<int, Opportunity>
     */
    #[ORM\OneToMany(targetEntity: Opportunity::class, mappedBy: 'customer')]
    private Collection $opportunities;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'customer')]
    private Collection $projects;

    #[ORM\Column(enumType: CustomerStatus::class)]
    private ?CustomerStatus $status = null;

    public function __construct()
    {
        $this->opportunities = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAcquisitionDate(): ?\DateTimeInterface
    {
        return $this->acquisitionDate;
    }

    public function setAcquisitionDate(\DateTimeInterface $acquisitionDate): static
    {
        $this->acquisitionDate = $acquisitionDate;

        return $this;
    }

    public function isBusiness(): bool
    {
        return $this->isBusiness;
    }

    public function setIsBusiness(bool $isBusiness): self
    {
        $this->isBusiness = $isBusiness;
        return $this;
    }

    public function getOperation(): ?Operation
    {
        return $this->operation;
    }

    public function setOperation(?Operation $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Opportunity>
     */
    public function getOpportunities(): Collection
    {
        return $this->opportunities;
    }

    public function addOpportunity(Opportunity $opportunity): static
    {
        if (!$this->opportunities->contains($opportunity)) {
            $this->opportunities->add($opportunity);
            $opportunity->setCustomer($this);
        }

        return $this;
    }

    public function removeOpportunity(Opportunity $opportunity): static
    {
        if ($this->opportunities->removeElement($opportunity)) {
            // set the owning side to null (unless already changed)
            if ($opportunity->getCustomer() === $this) {
                $opportunity->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setCustomer($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getCustomer() === $this) {
                $project->setCustomer(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?CustomerStatus
    {
        return $this->status;
    }

    public function setStatus(CustomerStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}

<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\OpportunityStatus;
use App\Repository\OpportunityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OpportunityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Opportunity
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\ManyToOne(inversedBy: 'opportunities')]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $expectedClosingDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'opportunities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Operation $operation = null;

    #[ORM\Column(enumType: OpportunityStatus::class)]
    private ?OpportunityStatus $status = null;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getExpectedClosingDate(): ?\DateTimeInterface
    {
        return $this->expectedClosingDate;
    }

    public function setExpectedClosingDate(\DateTimeInterface $expectedClosingDate): static
    {
        $this->expectedClosingDate = $expectedClosingDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    public function getStatus(): ?OpportunityStatus
    {
        return $this->status;
    }

    public function setStatus(OpportunityStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}

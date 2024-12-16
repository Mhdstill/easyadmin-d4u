<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Notification
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $targetPath = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $source = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Operation $operation = null;

    /**
     * @var Collection<int, NotificationRead>
     */
    #[ORM\OneToMany(targetEntity: NotificationRead::class, mappedBy: 'notification')]
    private Collection $notificationReads;

    public function __construct()
    {
        $this->notificationReads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getTargetPath(): ?string
    {
        return $this->targetPath;
    }

    public function setTargetPath(?string $targetPath): static
    {
        $this->targetPath = $targetPath;

        return $this;
    }

    public function getSource(): ?User
    {
        return $this->source;
    }

    public function setSource(?User $source): static
    {
        $this->source = $source;

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

    /**
     * @return Collection<int, NotificationRead>
     */
    public function getNotificationReads(): Collection
    {
        return $this->notificationReads;
    }

    public function addNotificationRead(NotificationRead $notificationRead): static
    {
        if (!$this->notificationReads->contains($notificationRead)) {
            $this->notificationReads->add($notificationRead);
            $notificationRead->setNotification($this);
        }

        return $this;
    }

    public function removeNotificationRead(NotificationRead $notificationRead): static
    {
        if ($this->notificationReads->removeElement($notificationRead)) {
            if ($notificationRead->getNotification() === $this) {
                $notificationRead->setNotification(null);
            }
        }

        return $this;
    }
}

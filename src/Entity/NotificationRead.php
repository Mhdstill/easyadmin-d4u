<?php

namespace App\Entity;

use App\Repository\NotificationReadRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;

#[ORM\Entity(repositoryClass: NotificationReadRepository::class)]
#[ORM\HasLifecycleCallbacks]
class NotificationRead
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notificationReads')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'notificationReads')]
    private ?Notification $notification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function setNotification(?Notification $notification): static
    {
        $this->notification = $notification;
        return $this;
    }
}

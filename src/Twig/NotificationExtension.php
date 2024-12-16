<?php

namespace App\Twig;

use App\Repository\NotificationRepository;
use App\Service\OperationContext;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private OperationContext $operationContext,
        private Security $security
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_notifications', [$this, 'getNotifications']),
        ];
    }

    public function getNotifications(): array
    {
        $user = $this->security->getUser();
        $operation = $this->operationContext->getCurrentOperation();
        if (!$user || !$operation) {
            return [];
        }

        return $this->notificationRepository->findUnreadByUser($user, $operation);
    }
} 
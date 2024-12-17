<?php

namespace App\Service;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\SecurityBundle\Security;

class NotificationService
{

    public function __construct(
        private NotificationRepository $notificationRepository,
        private AdminUrlGenerator $adminUrlGenerator,
        private Security $security,
        private OperationContext $operationContext
    ) {
    }

    public function createEANotification($entityInstance, string $action, bool $flush = false): Notification
    {
        $entityClass = get_class($entityInstance);
        $className = (new \ReflectionClass($entityClass))->getShortName();
        $crudController = sprintf('App\\Controller\\Admin\\%sCrudController', $className);
        $targetPath = $this->adminUrlGenerator
            ->setController($crudController)
            ->setAction(Action::INDEX)
            ->generateUrl();

        $content = method_exists($entityInstance, '__toString')
            ? sprintf("%s a été %s", $entityInstance->__toString(), $action)
            : sprintf("Un(e) %s a été %s", $className, $action);

        $operation = $this->operationContext->getCurrentOperation();
        return $this->notificationRepository->createNotification($operation, $content, $flush, $this->security->getUser(), $targetPath);
    }
}

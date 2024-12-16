<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Entity\NotificationRead;
use App\Entity\Operation;
use App\Service\NotificationService;
use App\Service\OperationContext;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
abstract class AdminController extends AbstractCrudController
{

    public function __construct(
        private OperationContext $operationContext,
        private RequestStack $requestStack,
        private NotificationService $notificationService,
        protected UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * Filtre les entités par le `operation` courant
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $entityManager = $this->container->get('doctrine')->getManagerForClass($entityDto->getFqcn());
        $operation = $this->operationContext->getCurrentOperation();
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($operation) {
            $metadata = $entityManager->getClassMetadata($entityDto->getFqcn());

            // Cas 1: Relation directe (ManyToOne ou OneToOne)
            if ($metadata->hasAssociation('operation')) {
                $queryBuilder->andWhere('entity.operation = :operation')
                    ->setParameter('operation', $operation);
            }
            // Cas 2: Relation ManyToMany
            elseif ($metadata->hasAssociation('operations')) {
                $queryBuilder->innerJoin('entity.operations', 'op')
                    ->andWhere('op = :operation')
                    ->setParameter('operation', $operation);
            }
            // Cas 3: Pas de relation avec Operation, on ne filtre pas
        }

        return $queryBuilder;
    }

    /**
     * Persiste l'entité avec le operation courant lors de la création.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $operation = $this->operationContext->getCurrentOperation();

        if ($operation) {
            $metadata = $entityManager->getClassMetadata(get_class($entityInstance));

            // Cas 1: Relation directe (ManyToOne ou OneToOne)
            if ($metadata->hasAssociation('operation')) {
                $entityInstance->setOperation($operation);
            }
            // Cas 2: Relation ManyToMany
            elseif ($metadata->hasAssociation('operations')) {
                $entityInstance->addOperation($operation);
            }
            // Cas 3: Pas de relation avec Operation, on ne fait rien

            $this->notificationService->createEANotification($entityInstance, "créé(e)");
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->notificationService->createEANotification($entityInstance, "modifié(e)");
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->notificationService->createEANotification($entityInstance, "supprimé(e)");
        parent::deleteEntity($entityManager, $entityInstance);
    }

    public function edit(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();
        $this->checkEntityAccess($entity);
        return parent::edit($context);
    }

    public function delete(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();
        $this->checkEntityAccess($entity);
        return parent::delete($context);
    }

    public function detail(AdminContext $context)
    {
        $entity = $context->getEntity()->getInstance();
        $this->checkEntityAccess($entity);
        return parent::detail($context);
    }

    #[Route('/admin/switch-operation/{id}', name: 'admin_switch_operation')]
    public function switchOperation(Operation $operation, Request $request): Response
    {
        $this->operationContext->setCurrentOperation($operation);

        // Récupère l'URL précédente
        $referer = $request->headers->get('referer');

        // Si pas de referer, retourne à l'admin
        return $this->redirect($referer ?: $this->generateUrl('admin'));
    }

    #[Route('/admin/notification/{id}', name: 'admin_read_notification')]
    public function notification(Notification $notification, EntityManagerInterface $entityManager, Request $request): Response
    {
        $notificationRead = new NotificationRead();
        $notificationRead->setNotification($notification);
        $notificationRead->setUser($this->getUser());
        $entityManager->persist($notificationRead);
        $entityManager->flush();

        if ($notification->getTargetPath()) {
            return $this->redirect($notification->getTargetPath());
        }

        return $this->redirect($this->generateUrl('admin'));
    }

    protected function checkEntityAccess($entity): void
    {
        $entityManager = $this->container->get('doctrine')->getManagerForClass(get_class($entity));
        $metadata = $entityManager->getClassMetadata(get_class($entity));
        $currentOperation = $this->operationContext->getCurrentOperation();

        if ($metadata->hasAssociation('operation')) {
            if ($currentOperation->getId() != $entity->getOperation()->getId()) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette ressource.');
            }
        } elseif ($metadata->hasAssociation('operations')) {
            if (!$entity->getOperations()->contains($currentOperation)) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette ressource.');
            }
        }
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Operation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
class OperationCrudController extends AbstractCrudController
{
    public function __construct(
        private NotificationService $notificationService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Operation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des opérations')
            ->setPageTitle('new', 'Créer une opération')
            ->setPageTitle('edit', 'Modifier l\'opération')
            ->setEntityLabelInSingular('Opération')
            ->setEntityLabelInPlural('Opérations')
            ->setPaginatorPageSize(5)
            ->setPaginatorRangeSize(4)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true)
        ;
    }


    public function configureActions(Actions $actions): Actions
    {
        /*
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            return $actions
                ->disable(Action::NEW, Action::DELETE);
        }
        */
        return $actions;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->notificationService->createEANotification($entityInstance, "créé(e)");
        parent::updateEntity($entityManager, $entityInstance);
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
    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addColumn(6),
            FormField::addFieldset('Informations'),
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
        ];
    }
}

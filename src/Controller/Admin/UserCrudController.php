<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AdminController
{

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            FormField::addColumn(6),
            FormField::addFieldset('Authentification'),
            TextField::new('email')
                ->setFormTypeOption('attr', ['placeholder' => 'Entrez votre email']),
            TextField::new('password')->setFormType(PasswordType::class)->hideOnIndex(),
            AssociationField::new('operations', 'Opérations')
                ->setFormTypeOptions([
                    'by_reference' => false,
                ])
                //   ->setFormTypeOption('autocomplete', true)
                ->setCrudController(OperationCrudController::class),


            FormField::addColumn(6),
            FormField::addFieldset('Informations'),
            TextField::new('firstName', 'Prénom'),
            TextField::new('lastName', 'Nom'    ),
            TextField::new('job', 'Poste'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des utilisateurs')
            ->setPageTitle('new', 'Créer un utilisateur')
            ->setPageTitle('edit', 'Modifier l\'utilisateur')
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPaginatorPageSize(5)
            ->setPaginatorRangeSize(4)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email')
            ->add('password')
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Hash the password if set
            if ($entityInstance->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
                $entityInstance->setPassword($hashedPassword);
            }

            // Add ROLE_SUB_ADMIN when the user is created
            $roles = $entityInstance->getRoles();
            if (!in_array('ROLE_SUB_ADMIN', $roles)) {
                $roles[] = 'ROLE_SUB_ADMIN';
                $entityInstance->setRoles($roles);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    // Override the updateEntity method to hash the password on user update
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
            $entityInstance->setPassword($hashedPassword);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}

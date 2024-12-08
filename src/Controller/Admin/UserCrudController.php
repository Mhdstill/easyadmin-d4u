<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setEntityLabelInSingular('Utilisateur')  // Singulier : Utilisateur
        ->setEntityLabelInPlural('Utilisateurs')   // Pluriel : Utilisateurs
        ->setPageTitle('index', 'Utilisateurs')    // Page d'index
        ->setPageTitle('new', 'Créer un utilisateur')  // Page de création
        ->setPageTitle('edit', 'Modifier un utilisateur') // Page d'édition
        ->setPageTitle('detail', 'Détails de l\'utilisateur'); // Page de détail
}


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('email'),
            AssociationField::new('shops', 'Magasins')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
         //   ->setFormTypeOption('autocomplete', true)
            ->setCrudController(ShopCrudController::class), 
            TextField::new('password', 'Mot de passe')->setFormType(PasswordType::class)->onlyOnForms(), // Only when creating
        ];
    }

    // Override the persistEntity method to hash password on user creation
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

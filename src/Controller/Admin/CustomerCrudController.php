<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Form\Type\CustomerStatusType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CustomerCrudController extends AdminController
{
    public static function getEntityFqcn(): string
    {
        return Customer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des clients')
            ->setPageTitle('new', 'Créer un client')
            ->setPageTitle('edit', 'Modifier le client')
            ->setPageTitle('detail', 'Détail du client')
            ->setEntityLabelInSingular('Client')
            ->setEntityLabelInPlural('Clients')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(5)
            ->setPaginatorRangeSize(4)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(6);
        yield FormField::addPanel('Informations principales');
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('firstname', 'Prénom');
        yield TextField::new('lastname', 'Nom');
        yield EmailField::new('email', 'Email');
        yield TelephoneField::new('phone', 'Téléphone');
        
        yield FormField::addColumn(6);
        yield FormField::addPanel('Informations complémentaires');
        yield TextareaField::new('address', 'Adresse')->hideOnIndex();
        yield DateField::new('acquisitionDate', 'Acquisition');
        yield ChoiceField::new('status', 'Statut')
            ->setFormType(CustomerStatusType::class);
        yield BooleanField::new('isBusiness', 'Professionnel')->hideOnIndex();
    }
} 
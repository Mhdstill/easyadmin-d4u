<?php

namespace App\Controller\Admin;

use App\Entity\Opportunity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use App\Form\Type\OpportunityStatusType;

class OpportunityCrudController extends AdminController
{
    public static function getEntityFqcn(): string
    {
        return Opportunity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des opportunités')
            ->setPageTitle('new', 'Créer une opportunité')
            ->setPageTitle('edit', 'Modifier l\'opportunité')
            ->setPageTitle('detail', 'Détail de l\'opportunité')
            ->setEntityLabelInSingular('Opportunité')
            ->setEntityLabelInPlural('Opportunités')
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
        yield TextField::new('title', 'Titre');
        yield AssociationField::new('customer', 'Client')
            ->setCrudController(CustomerCrudController::class);
        yield NumberField::new('amount', 'Montant');

        yield FormField::addColumn(6);
        yield FormField::addPanel('Suivi');
        yield DateField::new('expectedClosingDate', 'Date de clôture prévue');
        yield ChoiceField::new('status', 'Statut')
            ->setFormType(OpportunityStatusType::class);
        yield TextareaField::new('description', 'Description')->hideOnIndex();
    }
} 
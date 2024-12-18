<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\Type\ProjectStatusType;
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

class ProjectCrudController extends AdminController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des projets')
            ->setPageTitle('new', 'Créer un projet')
            ->setPageTitle('edit', 'Modifier le projet')
            ->setPageTitle('detail', 'Détail du projet')
            ->setEntityLabelInSingular('Projet')
            ->setEntityLabelInPlural('Projets')
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
        yield TextField::new('name', 'Nom');
        yield AssociationField::new('customer', 'Client')
            ->setCrudController(CustomerCrudController::class);
        yield NumberField::new('budget', 'Budget');

        yield FormField::addColumn(6);
        yield FormField::addPanel('Planning');
        yield DateField::new('startDate', 'Date de début');
        yield DateField::new('endDate', 'Date de fin');
        yield ChoiceField::new('status', 'Statut')
            ->setFormType(ProjectStatusType::class);
        yield TextareaField::new('description', 'Description')->hideOnIndex();
    }
} 
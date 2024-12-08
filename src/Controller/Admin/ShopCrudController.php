<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use App\Entity\Supplier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Magasin')  // Singulier : Magasin
            ->setEntityLabelInPlural('Magasins')   // Pluriel : Magasins
            ->setPageTitle('index', 'Magasins')    // Page d'index
            ->setPageTitle('new', 'Créer un magasin')  // Page de création
            ->setPageTitle('edit', 'Modifier un magasin') // Page d'édition
            ->setPageTitle('detail', 'Détails du magasin'); // Page de détail
    }


    public function configureFields(string $pageName): iterable
    {
        //$fields = parent::configureFields($pageName);
        $fields = [];
        $fields[] = TextField::new('name', 'Nom');
        $fields[] = NumberField::new('limitCharges', 'Limite à ne pas dépasser');
        return $fields;
    }

}

<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductImageFormType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Produits :')
            ->setPageTitle('new', 'Créer un produit')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('un produit');
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            FormField::addColumn(6),
            TextField::new('name', 'Nom du produit'),
            TextEditorField::new('description', 'Description du produit'),
            TextEditorField::new('introduction','description courte'),
            MoneyField::new('price', 'Prix du produit')
            ->setCurrency('EUR')
            ->setTextAlign('left')
            ->setFormTypeOption('divisor', 100),
            IntegerField::new('stock', 'Stock du produit'),
            IntegerField::new('capacity', 'Capacité du produit'),
            DateField::new('MDD', 'Date de péremption'),
            TextField::new('boticalName', 'Nom botanique'),
            TextField::new('partOfPlant', 'Partie de la plante'),
            TextField::new('extractionMethod', 'Méthode d\'extraction'),
            TextField::new('culture', 'mode de culture'),
            TextEditorField::new('advise', 'Conseils d\'utilisation'),
            TextEditorField::new('precautions', 'Précautions d\'utilisation'),
            CollectionField::new('images', 'Images')
            ->setEntryType(ProductImageFormType::class)
            ->setFormTypeOption('by_reference', false)
            ->hideOnIndex(),
            AssociationField::new('category', 'Catégorie du produit'),
            //BooleanField::new('isHighlighted', 'Mettre en avant'),
        ];
    }
    
}

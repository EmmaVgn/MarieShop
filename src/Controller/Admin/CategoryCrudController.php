<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;

class CategoryCrudController extends AbstractCrudController
{
    // protected $slugger;

    // public function __construct(SluggerInterface $slugger)
    // {
    //     $this->slugger = $slugger;
    // }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Catégories de produits')
            ->setPageTitle('new', 'Ajouter une catégorie')
            ->setDefaultSort(['id' => 'ASC'])
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('une catégorie');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->onlyOnIndex(),
            TextField::new('name', 'Nom de la catégorie')
            ->setFormTypeOptions(['attr' => ['placeholder' => 'Ex: Huiles essentielles']]),
            TextField::new('slug', 'Slug')->onlyOnIndex(),
        ];
    }
}

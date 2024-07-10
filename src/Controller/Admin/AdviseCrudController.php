<?php

namespace App\Controller\Admin;

use App\Entity\Advise;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\DependencyInjection\ContainerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdviseCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Advise::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Blog')
            ->setEntityLabelInPlural('Blogs')
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextareaField::new('subtitle', 'Sous-titre'),
            TextareaField::new('content', 'Contenu'),
            SlugField::new('slug')
                ->setTargetFieldName('title') // Spécifie le champ utilisé pour générer le slug
                ->setFormTypeOptions(['disabled' => true]), // Rendre le champ slug non modifiable dans le formulaire
            ImageField::new('image', 'Image')
                ->setBasePath('images/blog/')
                ->setUploadDir('public/images/blog')
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Advise) {
            $entityInstance->generateSlug($this->slugger);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Advise) {
            $entityInstance->generateSlug($this->slugger);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
    
}

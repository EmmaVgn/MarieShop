<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Images;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger) {}

    public function load(ObjectManager $manager): void
    {
        // Use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create('fr_FR');

        // Créer des catégories
        $categoryNames = ['Huiles essentielles', 'Recharges hydrolats'];
        $categoryArray = [];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug($this->slugger->slug($category->getName())->lower());
            $manager->persist($category);
            $categoryArray[] = $category; // Ajouter à la liste des catégories
        }

        // Créer des produits
        for ($p = 1; $p <= 20; $p++) {
            $product = new Product();
            $product->setName($faker->words(3, true)); // Utiliser words pour un nom plus réaliste
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(25000, 100000));
            $product->setStock($faker->numberBetween(1, 20));
            $product->setDescription($faker->text(150));
          
            
            // Associer une catégorie aléatoire à partir du tableau de catégories
            $product->setCategory($faker->randomElement($categoryArray));

        }

        $manager->flush();
    }
}

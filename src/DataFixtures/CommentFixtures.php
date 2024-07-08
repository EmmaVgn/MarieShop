<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CommentFixtures extends Fixture
{
    protected $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($c=0; $c < 300; $c++) { 
                    $comment = new Comment;
                    $comment->setFullname($faker->name())
                            ->setContent($faker->paragraph())
                            ->setRating(mt_rand(4,5))
                            ->setIsValid(false);

                    $manager->persist($comment);
                }

                $manager->flush();
            }
}
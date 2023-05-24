<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class AppFixtures extends Fixture
{   
    public function __construct(
       private PasswordHasherFactoryInterface $passwordHasherFactory, 
       private string $adminPassword,) { 
       }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $admin = new User();
        $admin->setRoles(['ROLE_SUPER_ADMIN']);
        $admin->setEmail('admin@gmail.com');
        $admin->setPassword($this->passwordHasherFactory->getPasswordHasher(User::class)->hash($this->adminPassword));
        $manager->persist($admin);

         $manager->flush();
    }
}

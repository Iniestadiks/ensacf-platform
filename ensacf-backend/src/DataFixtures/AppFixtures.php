<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un admin
        $admin = new Admin();
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpassword'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // Création d'un enseignant
        $teacher = new Teacher();
        $teacher->setEmail('teacher@example.com');
        $teacher->setRoles(['ROLE_TEACHER']);
        $teacher->setPassword($this->passwordHasher->hashPassword($teacher, 'teacherpassword'));
        $manager->persist($teacher);

        $manager->flush();
    }
}
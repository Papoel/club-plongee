<?php

namespace App\DataFixtures;

use App\Entity\Licence;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create(locale: 'fr_FR');
        /** @var User[] $users */
        $users = [];

        $admin = new User();
        $admin
            ->setFirstname(firstname: 'pascal')
            ->setLastname(lastname: 'briffard')
            ->setEmail(email: 'papoel@admin.fr')
            ->setRoles(roles: ['ROLE_ADMIN'])
            ->setAddress(address: '15 rue de la Liberté')
            ->setZipCode(zipCode: '59600')
            ->setTown(town: 'Maubeuge');
        /** @param array $certificats */
        $certificats = [
            'certificat_medical_2021.pdf',
            'certificat_medical_2022.pdf',
            'certificat_medical_2023.pdf',
        ];
        /* @phpstan-ignore-next-line */
        $admin->setCertificateMedical(certificateMedical: $faker->randomElement($array = $certificats));

        $hash = $this->passwordHasher->hashPassword(user: $admin, plainPassword: 'admin');
        $admin->setPassword(password: $hash);

        // Ajouter une licence à l'admin
        $licence = new Licence();
        $licence
            ->setNumber(number: '123456')
            ->setUserLicence(user_licence: $admin);

        $manager->persist($licence);
        $manager->persist($admin);
        $users[] = $admin;

        // Generate 10 users
        for ($adherent = 0; $adherent < 10; ++$adherent) {
            $user = new User();
            $user
                ->setFirstname(firstname: $faker->firstName())
                ->setLastname(lastname: $faker->lastName())
                ->setEmail(email: $faker->email())
                ->setRoles(roles: ['ROLE_USER'])
                ->setAddress(address: $faker->streetAddress())
                // Transforme le code postal en string pour éviter les erreurs de type
                ->setZipCode(zipCode: (string) $faker->randomNumber($nbDigits = 5, $strict = true))
                ->setTown(town: $faker->city());

            /** @param array $certificats */
            $certificats = [
                'certificat_medical_2021.pdf',
                'certificat_medical_2022.pdf',
                'certificat_medical_2023.pdf',
            ];
            /* @phpstan-ignore-next-line */
            $admin->setCertificateMedical(certificateMedical: $faker->randomElement($array = $certificats));
            $hash = $this->passwordHasher->hashPassword(user: $user, plainPassword: 'plongee');
            $user->setPassword(password: $hash);

            // Ajouter une licence à adherent
            $licence = new Licence();
            $licence
                ->setNumber(number: (string) $faker->randomNumber($nbDigits = 6))
                ->setUserLicence(user_licence: $user);

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}

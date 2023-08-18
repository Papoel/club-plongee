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
            ->setGenre(genre: (string) 'Homme')
            ->setFirstname(firstname: 'pascal')
            ->setLastname(lastname: 'briffard')
            ->setEmail(email: 'papoel@admin.fr')
            ->setRoles(roles: ['ROLE_ADMIN', 'ROLE_ADHERENT'])
            ->setAddress(address: '15 rue de la Liberté')
            ->setZipCode(zipCode: (string) '59600')
            ->setCity(city: 'Maubeuge')
            ->setCountry(country: (string) 'France')
            ->setPhone(phone: '0669399414')
            ->setBio(bio: 'Je suis le super admin')
        ;

        /** @param array $certificats */
        $certificats = [
            'certificat_medical_2021.pdf',
            'certificat_medical_2022.pdf',
            'certificat_medical_2023.pdf',
        ];
        // /* @phpstan-ignore-next-line */
        $admin->setCertificateMedical(certificateMedical: $faker->randomElement($array = $certificats));

        $hash = $this->passwordHasher->hashPassword(user: $admin, plainPassword: 'admin1234');
        $admin->setPassword(password: $hash);

        // Ajouter une licence à l'admin
        $licence = new Licence();
        $licence
            ->setNumber(number: 'A-17-776251')
            ->setUserLicence(user_licence: $admin);

        $manager->persist($licence);
        $manager->persist($admin);
        $users[] = $admin;

        // Generate 5 users
        for ($adherent = 1; $adherent <= 5; ++$adherent) {
            $user = new User();
            $genre = $faker->randomElement(['Homme', 'Femme', 'Autre']);
            $country = $faker->randomElement(['France', 'Belgique']);
            $divingLevel = $faker->randomElement(['1', '2', '3']);

            $user
                /* @phpstan-ignore-next-line */
                ->setGenre(genre: (string) $genre)
                ->setFirstname(firstname: $faker->firstName())
                ->setLastname(lastname: $faker->lastName())
                ->setEmail(email: 'user'.$adherent.'@test.fr')
                ->setAddress(address: $faker->streetAddress())
                ->setZipCode(zipCode: (string) $faker->randomNumber($nbDigits = 5, $strict = true))
                ->setCity(city: $faker->city())
                /* @phpstan-ignore-next-line */
                ->setCountry(country: (string) $country)
                /* @phpstan-ignore-next-line */
                ->setDivingLevel(diving_level: (int) $divingLevel)
                ->setPhone(phone: $faker->phoneNumber())
            ;

            // /** @param array $certificats */
            $certificats = [
                'certificat_medical_2021.pdf',
                'certificat_medical_2022.pdf',
                'certificat_medical_2023.pdf',
            ];
            // /* @phpstan-ignore-next-line */
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

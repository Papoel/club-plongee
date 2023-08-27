<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create(locale: 'fr_FR');

        for ($i = 1; $i <= 5; ++$i) {
            $contact = new Contact();
            $contact
                ->setFullname($faker->name())
                ->setEmail($faker->email())
                ->setSubject(subject: 'Demande n°'.$i)
                ->setMessage($faker->text())
            ;
            $manager->persist($contact);
        }

        $manager->flush();
    }
}

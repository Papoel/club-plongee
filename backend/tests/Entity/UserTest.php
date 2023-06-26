<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\Abstract\EntityTestCase;
use DateTimeImmutable;
use Exception;

class UserTest extends EntityTestCase
{
    public function getEntityUser(): User
    {
        $user = new User();

        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setEmail(email: 'test@email.fr');
        $user->setPassword(password: 'Password1');
        $user->setRoles(roles: ['ROLE_ADMIN']);
        $user->setAddress(address: '15 rue de la Liberté');
        $user->setCity(city: 'Maubeuge');
        $user->setZipCode(zipCode: '59600');
        $user->setPhone(phone: '0606060606');
        $user->setCertificateMedical(certificateMedical: 'certificat médical 2023');
        $user->setCreatedAt(createdAt: new DateTimeImmutable());
        $user->setUpdatedAt(updatedAt: null);
        $user->setLicence(licence: null);

        return $user;
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityUserIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityUser(), count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetId(): void
    {
        $user = $this->getEntityUser();
        self::assertNull($user->getId());

        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetEmail(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: 'test@email.fr', actual: $user->getEmail());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetUserIdentifier(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals('test@email.fr', $user->getUserIdentifier());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetRoles(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setEmail(email: 'email2@test.fr');
        $user->setPassword(password: 'MotDePasse1');

        self::assertContains(needle: User::ROLE_ADHERENT, haystack: $user->getRoles());
        $this->assertValidationErrorsCount($user, count: 0);

        $roles = ['ROLE_ADMIN'];
        $user->setRoles($roles);

        self::assertContains(needle: User::ROLE_ADHERENT, haystack: $user->getRoles());
        self::assertContains(needle: User::ROLE_ADMIN, haystack: $user->getRoles());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetPassword(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: 'Password1', actual: $user->getPassword());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetFirstname(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: 'Pascal', actual: $user->getFirstname());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetLastname(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: 'Briffard', actual: $user->getLastname());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetAddress(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: '15 rue de la Liberté', actual: $user->getAddress());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetCity(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: 'Maubeuge', actual: $user->getCity());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetZipCode(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: '59600', actual: $user->getZipCode());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetPhone(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: '0606060606', actual: $user->getPhone());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetCertificateMedical(): void
    {
        $user = $this->getEntityUser();

        self::assertEquals(expected: 'certificat médical 2023', actual: $user->getCertificateMedical());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetCreatedAt(): void
    {
        $user = $this->getEntityUser();

        self::assertInstanceOf(expected: DateTimeImmutable::class, actual: $user->getCreatedAt());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetUpdatedAt(): void
    {
        $user = $this->getEntityUser();

        self::assertNull($user->getUpdatedAt());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetLicence(): void
    {
        $user = $this->getEntityUser();

        self::assertNull($user->getLicence());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetFirstname(): void
    {
        $user = $this->getEntityUser();
        $user->setFirstname(firstname: 'Luc');

        self::assertEquals(expected: 'Luc', actual: $user->getFirstname());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetLastname(): void
    {
        $user = $this->getEntityUser();
        $user->setLastname(lastname: 'Skywalker');

        self::assertEquals(expected: 'Skywalker', actual: $user->getLastname());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetEmail(): void
    {
        $user = $this->getEntityUser();
        $user->setEmail(email: 'set@email.fr');

        self::assertEquals(expected: 'set@email.fr', actual: $user->getEmail());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetPassword(): void
    {
        $user = $this->getEntityUser();
        $user->setPassword(password: 'MotDePasse2');

        self::assertEquals(expected: 'MotDePasse2', actual: $user->getPassword());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetAddress(): void
    {
        $user = $this->getEntityUser();
        $user->setAddress(address: '15 rue de la Paix');

        self::assertEquals(expected: '15 rue de la Paix', actual: $user->getAddress());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetCity(): void
    {
        $user = $this->getEntityUser();
        $user->setCity(city: 'Paris');

        self::assertEquals(expected: 'Paris', actual: $user->getCity());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetZipCode(): void
    {
        $user = $this->getEntityUser();
        $user->setZipCode(zipCode: '75000');

        self::assertEquals(expected: '75000', actual: $user->getZipCode());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetPhone(): void
    {
        $user = $this->getEntityUser();
        $user->setPhone(phone: '0605040302');

        self::assertEquals(expected: '0605040302', actual: $user->getPhone());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function SetCertificateMedical(): void
    {
        $user = $this->getEntityUser();
        $user->setCertificateMedical(certificateMedical: 'certificat médical 2022');

        self::assertEquals(expected: 'certificat médical 2022', actual: $user->getCertificateMedical());
        $this->assertValidationErrorsCount($user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function PasswordWithoutMajuscule(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'motdepasse212');
        $user->setEmail(email: 'test@email.fr');

        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     *
     * @test
     * @throws Exception
     */
    public function passwordLessThan8Characters(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setEmail(email: 'test@email.fr');

        $password = 'Mdp1';
        $user->setPassword(password: $password);

        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function PasswordMoreThan25Characters(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setEmail(email: 'test@email.fr');

        // Use str_repeat to repeat a character 26 times
        $password = str_repeat(string: 'Mdp1', times: 26);
        $user->setPassword(password: $password);

        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function PasswordWithoutMinuscule(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MOTDEPASSE212');
        $user->setEmail(email: 'test@email.fr');

        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function PasswordWithoutNumber(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'Motdepasse');
        $user->setEmail(email: 'test@email.fr');

        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

/**
     * @test
     * @throws Exception
     */
    public function PasswordIsBlank(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: '');
        $user->setEmail(email: 'test@email.fr');

        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function FirstnameIsBlank(): void
    {
        $user = new User();
        $user->setFirstname(firstname: '');
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        // Retourne 2 erreurs (1 pour le champ vide et 1 pour le min de caractères)
        $this->assertValidationErrorsCount(entity: $user, count: 2);
    }

    /**
     * @test
     * @throws Exception
     */
    public function FirstnameLessThan3Characters(): void
    {
        $user = new User();
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $firstname = 'Pa';
        $user->setFirstname(firstname: $firstname);
        $this->assertValidationErrorsCount(entity: $user, count: 2);
    }

    /**
     * @test
     * @throws Exception
     */
    public function FirstnameIsBetween3And50Characters(): void
    {
        $user = new User();
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $firstname = str_repeat(string: 'a', times: 51);
        $user->setFirstname(firstname: $firstname);
        $this->assertValidationErrorsCount(entity: $user, count: 1);

        $firstname = str_repeat(string: 'a', times: 3);
        $user->setFirstname(firstname: $firstname);
        $this->assertValidationErrorsCount(entity: $user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function FirstnameContainNumber(): void
    {
        $user = new User();
        $user->setFirstname(firstname: '');
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $firstname = 'Pascal1';
        $user->setFirstname(firstname: $firstname);
        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function FirstnameWithDashIsValid(): void
    {
        $user = new User();
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $firstname = 'Pascal-Briffard';
        $user->setFirstname(firstname: $firstname);
        $this->assertValidationErrorsCount(entity: $user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function FirstnameWithSpecialCharactersOrSpaceIsInvalid(): void
    {
        $user = new User();
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MotDePasse1');
        $user->setFirstname(firstname: 'Pascal');
        $user->setEmail(email: 'test@email.fr');

        $specialCharacters = ['@', '*', '$', '#', '!', '?', '%', '^', ' '];
        foreach ($specialCharacters as $specialCharacter) {
            $firstname = 'Pascal' . $specialCharacter . 'Briffard';
            $user->setFirstname(firstname: $firstname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $firstname = $specialCharacter . 'PascalBriffard';
            $user->setFirstname(firstname: $firstname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $firstname = 'PascalBriffard' . $specialCharacter;
            $user->setFirstname(firstname: $firstname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $firstname = $specialCharacter . 'PascalBriffard' . $specialCharacter;
            $user->setFirstname(firstname: $firstname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $firstname = 'Pascal' . $specialCharacter . 'Briffard' . $specialCharacter;
            $user->setFirstname(firstname: $firstname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }
    }

    /**
     * @test
     * @throws Exception
     */
    public function LastnameIsBlank(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $lastname = '';
        $user->setLastname(lastname: $lastname);
        $this->assertValidationErrorsCount(entity: $user, count: 2);
    }

    /**
     * @test
     * @throws Exception
     */
    public function LastnameLessThan2Characters(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setLastname(lastname: 'Briffard');
        $user->setEmail(email: 'test@email.fr');

        $lastname = 'B';
        $user->setLastname(lastname: $lastname);
        $this->assertValidationErrorsCount(entity: $user, count: 2);
    }

    /**
     * @test
     * @throws Exception
     */
    public function LastnameIsBetween2And60Characters(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setLastname(lastname: 'Briffard');
        $user->setEmail(email: 'test@email.fr');

        $lastname = str_repeat(string: 'a', times: 61);
        $user->setLastname(lastname: $lastname);
        $this->assertValidationErrorsCount(entity: $user, count: 1);

        $lastname = str_repeat(string: 'a', times: 2);
        $user->setLastname(lastname: $lastname);
        $this->assertValidationErrorsCount(entity: $user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function LastnameContainNumber(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $lastname = 'Briffard1';
        $user->setLastname(lastname: $lastname);
        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function LastnameWithDashIsValid(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $lastname = 'Pascal-Briffard';
        $user->setLastname(lastname: $lastname);
        $this->assertValidationErrorsCount(entity: $user, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function LastnameWithSpecialCharactersOrSpaceIsInvalid(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'test@email.fr');

        $specialCharacters = ['@', '*', '$', '#', '!', '?', '%', '^', ' '];
        foreach ($specialCharacters as $specialCharacter) {
            $lastname = 'Pascal' . $specialCharacter . 'Briffard';
            $user->setLastname(lastname: $lastname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $lastname = $specialCharacter . 'PascalBriffard';
            $user->setLastname(lastname: $lastname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $lastname = 'PascalBriffard' . $specialCharacter;
            $user->setLastname(lastname: $lastname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $lastname = $specialCharacter . 'PascalBriffard' . $specialCharacter;
            $user->setLastname(lastname: $lastname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }

        foreach ($specialCharacters as $specialCharacter) {
            $lastname = 'Pascal' . $specialCharacter . 'Briffard' . $specialCharacter;
            $user->setLastname(lastname: $lastname);
            $this->assertValidationErrorsCount(entity: $user, count: 1);
        }
    }

    /**
     * @test
     * @throws Exception
     */
    public function EmailValidation(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setLastname(lastname: 'Briffard');
        $user->setPassword(password: 'MotDePasse1');
        $user->setEmail(email: 'invalid_email');

        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function EmailIsBlank(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setLastname(lastname: 'Briffard');

        $email = '';
        $user->setEmail(email: $email);
        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function EmailIsAlreadyUsed(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setLastname(lastname: 'Briffard');
        $user->setEmail(email: 'test@email.fr');

        self::getContainer()->get('doctrine')->getManager()->persist($user);
        self::getContainer()->get('doctrine')->getManager()->flush();

        $user2 = new User();
        $user2->setFirstname(firstname: 'Pascal');
        $user2->setPassword(password: 'MotDePasse1');
        $user2->setLastname(lastname: 'Briffard');
        $user2->setEmail(email: $user->getEmail());

        $this->assertValidationErrorsCount(entity: $user2, count: 1);
    }

    /**
     * @test
     * @throws Exception
     */
    public function EmailIsMoreThan180Characters(): void
    {
        $user = new User();
        $user->setFirstname(firstname: 'Pascal');
        $user->setPassword(password: 'MotDePasse1');
        $user->setLastname(lastname: 'Briffard');

        $name = str_repeat(string: 'a', times: 180);
        $email = $name . '@gmail.com';
        $user->setEmail(email: $email);
        $this->assertValidationErrorsCount(entity: $user, count: 1);
    }
}

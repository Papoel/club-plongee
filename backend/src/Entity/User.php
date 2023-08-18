<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADHERENT = 'ROLE_ADHERENT';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const TIMEZONE = 'Europe/Paris';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'adresse email ne doit pas contenir plus de {{ limit }} caractères.')]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    #[Assert\NotBlank(message: 'L\'adresse email est une information obligatoire.')]
    private string $email;

    /**
     * @var array<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Length(
        min: 8,
        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.')]
    #[Assert\NotCompromisedPassword(message: 'Le mot de passe a été compromis. Veuillez en choisir un autre.')]
    #[Assert\NotBlank(message: 'Le mot de passe est une information obligatoire.')]
    private string $password;

    #[ORM\Column(length: 50)]
    #[Assert\NotNull(message: 'Le prénom est une information obligatoire.')]
    #[Assert\NotBlank(message: 'Le prénom est une information obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le prénom ne doit pas contenir plus de {{ limit }} caractères.')]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÀ-ÿ|-]{3,}$/',
        message: 'Le prénom ne doit contenir que des lettres, mais si vous êtes le fils d\'Elon Musk(X Æ A-12), écrivez simplement Ex-ash-a-twelve comme cela se prononce.')]
    private string $firstname;

    #[ORM\Column(length: 60)]
    #[Assert\Length(
        min: 2,
        max: 60,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne doit pas contenir plus de {{ limit }} caractères.')]
    #[Assert\NotBlank(message: 'Le nom est une information obligatoire.')]
    #[Assert\Regex(
        pattern: '/^[A-Za-zÀ-ÿ|-]{2,}$/',
        message: 'Le nom ne doit contenir que des lettres.')]
    private string $lastname;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Assert\Regex(
        pattern: '/^[0-9]{5}$/',
        message: 'Le code postal doit contenir 5 chiffres.')]
    private ?string $zipCode = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $certificateMedical = null;

    #[ORM\OneToOne(inversedBy: 'user_licence', cascade: ['persist', 'remove'])]
    private ?Licence $licence = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^0[1-9]([-. ]?[0-9]{2}){4}$/',
        message: 'Le numéro de téléphone doit contenir 10 chiffres et commencer par 0, ou il peut être vide.')]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $diving_level = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column]
    private bool $isActive;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $account_deletion_request = null;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(timezone: new \DateTimeZone(timezone: self::TIMEZONE));
        $this->isActive = true;

        if (empty($this->roles)) {
            $this->roles = [self::ROLE_ADHERENT];
        }
    }

    public function getFullname(): string
    {
        // Capitalize the first letter of the firstname and the lastname
        $firstname = ucfirst(strtolower($this->firstname));
        $lastname = ucfirst(strtolower($this->lastname));

        return $firstname.' '.$lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_ADHERENT;

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCertificateMedical(): ?string
    {
        return $this->certificateMedical;
    }

    public function setCertificateMedical(?string $certificateMedical): static
    {
        $this->certificateMedical = $certificateMedical;

        return $this;
    }

    public function getLicence(): ?Licence
    {
        return $this->licence;
    }

    public function setLicence(?Licence $licence): static
    {
        $this->licence = $licence;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDivingLevel(): ?int
    {
        return $this->diving_level;
    }

    public function setDivingLevel(?int $diving_level): static
    {
        $this->diving_level = $diving_level;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function isIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getAccountDeletionRequest(): ?\DateTimeImmutable
    {
        return $this->account_deletion_request;
    }

    public function setAccountDeletionRequest(?\DateTimeImmutable $account_deletion_request): static
    {
        $this->account_deletion_request = $account_deletion_request;

        return $this;
    }
}

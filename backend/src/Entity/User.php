<?php

namespace App\Entity;

use App\Repository\UserRepository;
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
        max: 25,
        maxMessage: 'Le mot de passe ne doit pas contenir plus de {{ limit }} caractères.')]
    #[Assert\Regex(// Le mot de passe doit contenir au minimum 8 caractères, au moins une lettre majuscule, une lettre minuscule, et un chiffre.
        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/',
        // '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/',
        message: 'Le mot de passe doit contenir au minimum 8 caractères, au moins une lettre majuscule, une lettre minuscule, et un chiffre.'
    )]
    #[Assert\NotCompromisedPassword(message: 'Le mot de passe a été compromis. Veuillez en choisir un autre.')]
    #[Assert\NotBlank(message: 'Le mot de passe est une information obligatoire.')]
    private string $password;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le prénom ne doit pas contenir plus de {{ limit }} caractères.')]
    #[Assert\NotBlank(message: 'Le prénom est une information obligatoire.')]
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
    private ?string $zipCode = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $certificateMedical = null;

    #[ORM\OneToOne(inversedBy: 'user_licence', cascade: ['persist', 'remove'])]
    private ?Licence $licence = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

        if (empty($this->roles)) {
            $this->roles = [self::ROLE_ADHERENT];
        }
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
}

<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtAndUpdatedAtTrait;
use App\Repository\LicenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LicenceRepository::class)]
#[ORM\Table(name: 'licences')]
#[ORM\HasLifecycleCallbacks]
class Licence
{
    use CreatedAtAndUpdatedAtTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expireAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $diving_level = null;

    #[ORM\OneToOne(mappedBy: 'licence', cascade: ['persist', 'remove'])]
    private ?User $user_licence = null;

    public function __toString(): string
    {
        /* phpstan ignore-next-line */
        return $this->number ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getUserLicence(): ?User
    {
        return $this->user_licence;
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

    public function setUserLicence(?User $user_licence): static
    {
        // unset the owning side of the relation if necessary
        if (null === $user_licence && null !== $this->user_licence) {
            $this->user_licence->setLicence(null);
        }

        // set the owning side of the relation if necessary
        if (null !== $user_licence && $user_licence->getLicence() !== $this) {
            $user_licence->setLicence($this);
        }

        $this->user_licence = $user_licence;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTimeImmutable $expireAt): static
    {
        $this->expireAt = $expireAt;

        return $this;
    }
}

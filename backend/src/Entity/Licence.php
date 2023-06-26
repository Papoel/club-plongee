<?php

namespace App\Entity;

use App\Repository\LicenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LicenceRepository::class)]
class Licence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $number = null;

    #[ORM\OneToOne(mappedBy: 'licence', cascade: ['persist', 'remove'])]
    private ?User $user_licence = null;

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
}

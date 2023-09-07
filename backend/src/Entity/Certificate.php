<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtAndUpdatedAtTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\CertificateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CertificateRepository::class)]
#[ORM\Table(name: 'certificates')]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
class Certificate
{
    use UuidTrait;
    use CreatedAtAndUpdatedAtTrait;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $medicalCertificate = null;

    #[Vich\UploadableField(mapping: 'user_medical_certificate', fileNameProperty: 'medicalCertificate')]
    private ?File $medicalCertificateFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $originalFileName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $expireAt = null;

    #[ORM\ManyToOne(inversedBy: 'certificates')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user = null;

    public function getMedicalCertificate(): ?string
    {
        return $this->medicalCertificate;
    }

    public function setMedicalCertificate(?string $medicalCertificate): static
    {
        $this->medicalCertificate = $medicalCertificate;

        return $this;
    }

    public function getMedicalCertificateFile(): ?File
    {
        return $this->medicalCertificateFile;
    }

    /**
     * @throws \Exception
     */
    public function setMedicalCertificateFile(File $medicalCertificateFile = null): void
    {
        $this->medicalCertificateFile = $medicalCertificateFile;

        if (null !== $medicalCertificateFile) {
            $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
            $this->updatedAt = new \DateTimeImmutable(timezone: $timezone);
        }
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName(?string $originalFileName): static
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

    public function getExpireAt(): ?\DateTime
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTime $expireAt): static
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}

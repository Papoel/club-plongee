<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le titre doit contenir au maximum {{ limit }} caractères'
    )]
    private string $title;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $all_day = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $recurrent = false;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $background_color = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $border_color = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true)]
    private ?string $text_color = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $daysOfWeek = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, length: 5, nullable: true)]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, length: 5, nullable: true)]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startRecur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endRecur = null;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    private ?string $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frequency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isAllDay(): ?bool
    {
        return $this->all_day;
    }

    public function setAllDay(bool $all_day): static
    {
        $this->all_day = $all_day;

        return $this;
    }

    public function isRecurrent(): ?bool
    {
        return $this->recurrent;
    }

    public function setRecurrent(bool $recurrent): static
    {
        $this->recurrent = $recurrent;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->background_color;
    }

    public function setBackgroundColor(?string $background_color): static
    {
        $this->background_color = $background_color;

        return $this;
    }

    public function getBorderColor(): ?string
    {
        return $this->border_color;
    }

    public function setBorderColor(?string $border_color): static
    {
        $this->border_color = $border_color;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->text_color;
    }

    public function setTextColor(?string $text_color): static
    {
        $this->text_color = $text_color;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return array<string>|null
     */
    public function getDaysOfWeek(): ?array
    {
        return $this->daysOfWeek;
    }

    /**
     * @param array<string>|null $daysOfWeek
     */
    public function setDaysOfWeek(?array $daysOfWeek): static
    {
        $this->daysOfWeek = $daysOfWeek;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getStartRecur(): ?\DateTimeInterface
    {
        return $this->startRecur;
    }

    public function setStartRecur(?\DateTimeInterface $startRecur): static
    {
        $this->startRecur = $startRecur;

        return $this;
    }

    public function getEndRecur(): ?\DateTimeInterface
    {
        return $this->endRecur;
    }

    public function setEndRecur(?\DateTimeInterface $endRecur): static
    {
        $this->endRecur = $endRecur;

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(?string $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }
}

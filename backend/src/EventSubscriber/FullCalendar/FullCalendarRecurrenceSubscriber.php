<?php

declare(strict_types=1);

namespace App\EventSubscriber\FullCalendar;

use App\Entity\Calendar;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FullCalendarRecurrenceSubscriber implements EventSubscriberInterface
{
    /**
     * @return array<string, string[]>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::SUBMIT => ['onSubmit'],
        ];
    }

    /**
     * @throws \Exception
     */
    public function onSubmit(FormEvent $event): void
    {
        $data = $event->getData();

        if (!$data instanceof Calendar) {
            return;
        }

        $this->adjustRecurrenceEndDate($data);
    }

    /**
     * @throws \Exception
     */
    private function adjustRecurrenceEndDate(Calendar $data): void
    {
        if ($data->isRecurrent() && !empty($data->getDaysOfWeek())) {
            $lastOccurrenceDate = $this->calculateLastOccurrenceDate($data->getDaysOfWeek(), $data->getEndRecur());

            // Set la date de fin de récurrence
            $data->setEndRecur($lastOccurrenceDate);
        }
    }

    private function convertSundayIndex(int $dayIndex): int
    {
        // Le composant ChoiceType de Symfony utilise 0 pour dimanche, nous convertissons ici en 7 pour dimanche
        return (7 === $dayIndex) ? 0 : $dayIndex;
    }

    /**
     * @param int[]|string[] $daysOfWeek
     */
    private function calculateLastOccurrenceDate(array $daysOfWeek, ?\DateTimeInterface $endRecur): \DateTimeInterface
    {
        // Vérifier si $endRecur est null, et si c'est le cas, retourner une nouvelle instance de DateTime
        if (null === $endRecur) {
            return new \DateTime();
        }

        $dayIndex = (int) $endRecur->format(format: 'N');
        $lastOccurrenceDate = clone $endRecur;

        // Convertir le jour de la semaine au format utilisé par DateTime
        $dayIndex = $this->convertSundayIndex($dayIndex);

        // dd($lastOccurrenceDate, $dayIndex, $daysOfWeek, $daysOfWeek[0]);

        // Vérifier si le dayIndex existe dans le tableau $daysOfWeek
        if (in_array(needle: $dayIndex, haystack: $daysOfWeek, strict: true)) {
            // Si oui, ajouter un jour à la date $lastOccurrenceDate
            /* @phpstan-ignore-next-line */
            $lastOccurrenceDate->add(interval: new \DateInterval(duration: 'P1D'));
        }

        return $lastOccurrenceDate;
    }
}

<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @throws \JsonException
     */
    #[Route('/', name: 'app_home')]
    public function index(CalendarRepository $calendar): Response
    {
        $events = $calendar->findAll();
        $planing = [];

        // Convertir la valeur numérique de daysOfWeek en jours de semaine
        $daysOfWeekMap = [
            0 => 'dimanche',
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
        ];

        foreach ($events as $event) {
            // Permet de formater la date de début et de fin pour le calendrier
            $start = $event->getStart()?->format(format: 'Y-m-d H:i:s');
            $end = $event->getEnd()?->format(format: 'Y-m-d H:i:s');

            $startTime = $event->getStartTime()?->format(format: 'H:i');
            $endTime = $event->getEndTime()?->format(format: 'H:i');

            $startRecure = $event->getStartRecur()?->format(format: 'Y-m-d');
            $endRecure = $event->getEndRecur()?->format(format: 'Y-m-d');

            // Prendre la valeur de $startRecure et $startTime pour les concaténer et obtenir une valeur au format 'Y-m-d H:i:s'
            $startWhenEventIsRecurrent = $startRecure.' '.$startTime;
            $endWhenEventIsRecurrent = $endRecure.' '.$endTime;

            // Tableau qui contient les classes css pour le calendrier
            $borderColor = $event->getBorderColor();
            $classes = [
                'event-custom-style',
                'border-0',
                'rounded-1',
                'p-2',
                'ps-3',
                'border-5',
                'border-start',
                'border-'.$borderColor,
                'bg-'.$event->getBackgroundColor(),
                'text-'.$event->getTextColor(),
            ];

            $joursSemaine = [];

            if (null !== $event->getDaysOfWeek()) {
                foreach ($event->getDaysOfWeek() as $dayOfWeek) {
                    $joursSemaine[] = $daysOfWeekMap[$dayOfWeek];
                }
            }

            if ($event->isRecurrent()) {
                $planing[] = [
                    'id' => $event->getId(),
                    'recurrent' => $event->isRecurrent(),
                    'allDay' => $event->isAllDay(),
                    'title' => $event->getTitle(),
                    'description' => $event->getDescription(),
                    'start' => $startWhenEventIsRecurrent,
                    'end' => $endWhenEventIsRecurrent,
                    'backgroundColor' => $event->getBackgroundColor(),
                    'borderColor' => $event->getBorderColor(),
                    'textColor' => $event->getTextColor(),
                    'daysOfWeek' => $event->getDaysOfWeek(),
                    'duration' => $event->getDuration(),
                    'classNames' => $classes,
                    'startRecur' => $startRecure,
                    'endRecur' => $endRecure,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'joursSemaine' => $joursSemaine,
                    'frequency' => $event->getFrequency(),
                ];
            } else {
                $planing[] = [
                    'id' => $event->getId(),
                    'recurrent' => $event->isRecurrent(),
                    'allDay' => $event->isAllDay(),
                    'title' => $event->getTitle(),
                    'description' => $event->getDescription(),
                    'start' => $start,
                    'end' => $end,
                    'backgroundColor' => $event->getBackgroundColor(),
                    'borderColor' => $event->getBorderColor(),
                    'textColor' => $event->getTextColor(),
                    'duration' => $event->getDuration(),
                    'classNames' => $classes,
                ];
            }
        }

        $data = json_encode(value: $planing, flags: JSON_THROW_ON_ERROR);

        return $this->render(view: 'pages/home/index.html.twig', parameters: [
            'data' => $data,
            'events' => $events,
        ]);
    }
}

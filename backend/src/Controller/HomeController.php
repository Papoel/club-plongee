<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CalendarRepository $calendar): Response
    {
        $events = $calendar->findAll();

        $rdvs = [];
        $planing = [];

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

            if ($event->isRecurrent()) {
                $planing[] = [
                    'id' => $event->getId(),
                    'recurrent' => $event->isRecurrent(),
                    'allDay' => $event->isAllDay(),
                    'title' => $event->getTitle(),
                    'description' => $event->getDescription(),
                    'daysOfWeek' => $event->getDaysOfWeek(),
                    'frequency' => $event->getFrequency(),
                    'startRecur' => $startRecure,
                    'start' => $startWhenEventIsRecurrent,
                    'end' => $endWhenEventIsRecurrent,
                    'startTime' => $startTime,
                    'endRecur' => $endRecure,
                    'endTime' => $endTime,
                    'duration' => $event->getDuration(),
                    'backgroundColor' => $event->getBackgroundColor(),
                    'borderColor' => $event->getBorderColor(),
                    'textColor' => $event->getTextColor(),
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
                    /*'classNames' => [
                        'event-custom-style',
                        'text-nav',
                        'border-0',
                        'rounded-1',
                        'p-2',
                        'ps-3',
                        'border-start',
                        'border-5',
                    ],*/
                ];
            }
        }

        $data = json_encode(value: $planing, flags: JSON_THROW_ON_ERROR);

        // dd($data);

        return $this->render(view: 'pages/home/index.html.twig', parameters: compact(var_name: 'data'));
    }
}

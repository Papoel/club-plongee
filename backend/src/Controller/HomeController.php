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

        $rdvs = [];

        foreach ($events as $event) {
            // Permet de formater la date de début et de fin pour le calendrier
            $start = $event->getStart()?->format(format: 'Y-m-d H:i:s');
            $end = $event->getEnd()?->format(format: 'Y-m-d H:i:s');

            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $start,
                'end' => $end,
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->isAllDay(),
            ];
        }

        $data = json_encode(value: $rdvs, flags: JSON_THROW_ON_ERROR);

        return $this->render(view: 'pages/home/index.html.twig', parameters: compact(var_name: 'data'));
    }
}

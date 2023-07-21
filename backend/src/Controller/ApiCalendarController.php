<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/calendrier')]
class ApiCalendarController extends AbstractController
{
    /**
     * @throws \JsonException
     */
    #[Route('/{id}/edit', name: 'api_calendar_event_edit', methods: ['PUT'])]
    public function update(?Calendar $calendar, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Je récupère les données
        /** @var \stdClass $donnees */
        $donnees = json_decode(json: $request->getContent(), associative: false, depth: 512, flags: JSON_THROW_ON_ERROR);

        // Vérifier que les données obligatoires sont complètes
        if (
            isset($donnees->title, $donnees->start, $donnees->description, $donnees->allDay, $donnees->end)
            && null !== $donnees->backgroundColor && null !== $donnees->borderColor && null !== $donnees->textColor
        ) {
            // Les données sont complètes, je peux les utiliser, je renvoie une réponse 200 HTTP_OK
            $status = 200;

            // Vérifier si l'Id existe
            if (!$calendar) {
                // Je crée un nouvel objet
                $calendar = new Calendar();

                // Je change le status en 201, HTTP_CREATED
                $status = 201;
            }

            // J'hydrate l'objet
            $calendar->setTitle($donnees->title);
            $calendar->setDescription($donnees->description);
            $calendar->setStart(new \DateTime($donnees->start));

            if ($donnees->allDay) {
                $calendar->setEnd(new \DateTime($donnees->start));
            } else {
                $calendar->setEnd(new \DateTime($donnees->end));
            }

            // $calendar->setEnd($donnees->allDay ? new DateTime($donnees->start) : new DateTime($donnees->end));
            $calendar->setAllDay($donnees->allDay);
            $calendar->setBackgroundColor($donnees->backgroundColor);
            $calendar->setBorderColor($donnees->borderColor);
            $calendar->setTextColor($donnees->textColor);

            // Je sauvegarde en BDD
            $entityManager->persist($calendar);
            $entityManager->flush();

            // Je retourne une réponse 200 HTTP_OK
            return new Response('OK', $status);
        }

        // Les données sont incomplètes, je retourne une réponse 404 HTTP_NOT_FOUND
        return new Response(content: 'Données incomplètes', status: Response::HTTP_NOT_FOUND);
    }
}

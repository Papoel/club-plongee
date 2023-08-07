<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/calendrier')]
#[isGranted('ROLE_ADMIN')]
class CrudCalendarController extends AbstractController
{
    #[Route('/', name: 'app_calendar_index', methods: ['GET'])]
    public function index(CalendarRepository $calendarRepository): Response
    {
        return $this->render(view: 'crud_calendar/index.html.twig', parameters: [
            'calendars' => $calendarRepository->findAll(),
        ]);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/ajouter', name: 'app_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CalendarRepository $calendarRepository): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(type: CalendarType::class, data: $calendar);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez l'événement dans la base de données
            $calendarRepository->save(entity: $calendar, flush: true);

            return $this->redirectToRoute(route: 'app_calendar_index', parameters: [], status: Response::HTTP_SEE_OTHER);
        }

        return $this->render(view: 'crud_calendar/new.html.twig', parameters: [
            'calendar' => $calendar,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        return $this->render(view: 'crud_calendar/show.html.twig', parameters: [
            'calendar' => $calendar,
        ]);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/{id}/modifier', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, CalendarRepository $calendarRepository): Response
    {
        $form = $this->createForm(type: CalendarType::class, data: $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendarRepository->save($calendar, flush: true);

            return $this->redirectToRoute(route: 'app_calendar_index', parameters: [], status: Response::HTTP_SEE_OTHER);
        }

        return $this->render(view: 'crud_calendar/edit.html.twig', parameters: [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, CalendarRepository $calendarRepository): Response
    {
        $token = $request->request->get(key: '_token');

        if (is_string($token) && $this->isCsrfTokenValid(id: 'delete'.$calendar->getId(), token: $token)) {
            $calendarRepository->remove(entity: $calendar, flush: true);
        }

        return $this->redirectToRoute(route: 'app_calendar_index', parameters: [], status: Response::HTTP_SEE_OTHER);
    }
}

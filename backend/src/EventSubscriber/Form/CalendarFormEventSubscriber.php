<?php

namespace App\EventSubscriber\Form;

use App\Entity\Calendar;
use App\EventSubscriber\FullCalendar\FullCalendarRecurrenceSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CalendarFormEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => ['onPreSetData'],
            FormEvents::POST_SUBMIT => ['onPostSubmit'],
            FormEvents::SUBMIT => ['onSubmit'],
        ];
    }

    public function onPreSetData(FormEvent $event): void
    {
        $this->setDefaultDate($event);
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $this->setDefaultColors($event);
    }

    /**
     * @throws \Exception
     */
    public function onSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        /** @var Calendar $data */
        $data = $form->getData();

        // Call the FullCalendarRecurrenceSubscriber to adjust the recurrence end date
        $fullCalendarRecurrenceSubscriber = new FullCalendarRecurrenceSubscriber();
        $fullCalendarRecurrenceSubscriber->onSubmit($event);

        $this->setDateStartAndEndWhenRecurrentEvent($data);
    }

    protected function setDefaultColors(FormEvent $event): void
    {
        $calendar = $event->getData();

        /**
         * @var Calendar $calendar
         *
         * @description: Si l'utilisateur n'a pas choisi de couleur de fond, on lui en attribue une par défaut
         */
        if (null === $calendar->getBackgroundColor()) {
            $calendar->setBackgroundColor(background_color: 'white');
        }

        /*
         * @description: Si l'utilisateur n'a pas choisi de couleur de bordure, on lui en attribue une par défaut
         */
        if (null === $calendar->getBorderColor()) {
            $calendar->setBorderColor(border_color: 'primary');
        }

        /*
         * @description: Si l'utilisateur n'a pas choisi de couleur de texte, on lui en attribue une par défaut
         */
        if (null === $calendar->getTextColor()) {
            $calendar->setTextColor(text_color: 'primary');
        }
    }

    /**
     * @throws \Exception
     */
    protected function setDateStartAndEndWhenRecurrentEvent(Calendar $data): void
    {
        // Vérifier si reccurent est true
        if ($data->isRecurrent()) {
            // Si oui, on récupère la date de début (startRecur) et la date de fin (endRecur)
            $startRecur = $data->getStartRecur();
            $endRecur = $data->getEndRecur();
            $startTimeRecur = $data->getStartTime();
            $endTimeRecur = $data->getEndTime();

            if ($startRecur && $endRecur && $startTimeRecur && $endTimeRecur) {
                $startRecurFormatted = $startRecur->format(format: 'Y-m-d');
                $endRecurFormatted = $endRecur->format(format: 'Y-m-d');
                $startTimeRecurFormatted = $startTimeRecur->format(format: 'H:i:s');
                $endTimeRecurFormatted = $endTimeRecur->format(format: 'H:i:s');

                // J'alimente Start et End avec les valeurs de 'startRecur' et 'endRecur'
                $data->setStart(start: new \DateTime(datetime: $startRecurFormatted.' '.$startTimeRecurFormatted));
                $data->setEnd(end: new \DateTime(datetime: $endRecurFormatted.' '.$endTimeRecurFormatted));

                $data->setStartRecur(startRecur: new \DateTime(datetime: $startRecurFormatted.' '.$startTimeRecurFormatted));
                $data->setEndRecur(endRecur: new \DateTime(datetime: $endRecurFormatted.' '.$endTimeRecurFormatted));

                $data->setStartTime(startTime: $data->getStartRecur());
                $data->setEndTime(endTime: $data->getEndRecur());

                // Utiliser une ternaire pour vérifier si les dates de début et de fin sont non nulles avant d'appeler la méthode diff()
                $data->setDuration(
                    $data->getStart() && $data->getEnd() ? $data->getStart()
                        ->diff($data->getEnd())
                        ->format('%H:%I') : null
                );
            }
        }
    }

    private function setDefaultDate(FormEvent $event): void
    {
        // Prépopuller le champ 'start' avec la date du jour
        $form = $event->getForm();
        $data = $event->getData();

        /** @var Calendar $data */
        if (null === $data->getStart()) {
            $data->setStart(start: new \DateTime());
        }

        if (null === $data->getEnd()) {
            $data->setEnd(end: new \DateTime(datetime: '+1 hour'));
        }
    }
}

<?php

namespace App\Tests\Entity;

use App\Entity\Calendar;
use App\Tests\Abstract\EntityTestCase;
use Exception;

class CalendarTest extends EntityTestCase
{
    public function getEntityCalendar(): Calendar
    {
        $calendar = new Calendar();

        $calendar->setTitle(title: 'Test');
        $calendar->setStart(start: new \DateTime());
        $calendar->setEnd(end: new \DateTime(datetime: '+1 hour +30 minutes'));
        $calendar->setDescription(description: 'Test de l\'entité Calendar');
        $calendar->setAllDay(all_day: false);
        $calendar->setRecurrent(recurrent: false);

        return $calendar;
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityCalendarIsValid(): void
    {
        $this->assertValidationErrorsCount($this->getEntityCalendar(), count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function GetId(): void
    {
        $calendar = $this->getEntityCalendar();
        self::assertNull($calendar->getId());

        $this->assertValidationErrorsCount($calendar, count: 0);
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityCalendarIsNotValidBlankTitle(): void
    {
        $calendar = $this->getEntityCalendar();

        $calendar->setTitle(title: '');

        // Valider l'entité
        $violations = $this->validateEntity(entity: $calendar);
        $this->assertValidationErrorsCount(entity: $calendar, count: 2);

        // Vérifier les messages d'erreur spécifiques
        $this->assertContainsViolation(message: 'Cette valeur ne doit pas être vide.', violations: $violations);
        $this->assertContainsViolation(message: 'Le titre doit contenir au moins 3 caractères', violations: $violations);
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityCalendarIsNotValidLengthLessThan3Characters(): void
    {
        $calendar = $this->getEntityCalendar();

        $calendar->setTitle(title: 'aa');

        // Valider l'entité
        $violations = $this->validateEntity(entity: $calendar);
        $this->assertValidationErrorsCount(entity: $calendar, count: 1);

        // Vérifier les messages d'erreur spécifiques
        $this->assertContainsViolation(message: 'Le titre doit contenir au moins 3 caractères', violations: $violations);
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityCalendarIsNotValidLengthGreaterThan100Characters(): void
    {
        $calendar = $this->getEntityCalendar();

        $calendar->setTitle(title: str_repeat(string: 'a', times: 101));

        // Valider l'entité
        $violations = $this->validateEntity(entity: $calendar);
        $this->assertValidationErrorsCount(entity: $calendar, count: 1);

        // Vérifier les messages d'erreur spécifiques
        $this->assertContainsViolation(message: 'Le titre doit contenir au maximum 100 caractères', violations: $violations);
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityCalendarIsValidDescriptionBlank(): void
    {
        $calendar = $this->getEntityCalendar();

        $calendar->setDescription(description: '');

        $this->assertValidationErrorsCount($calendar, count: 0);
    }
}

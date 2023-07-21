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

        $this->assertValidationErrorsCount($calendar, count: 2 ,
            expectedMessage: 'Le titre doit contenir au moins 3 caractères'
        );
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityCalendarIsNotValidLengthLessThan3Characters(): void
    {
        $calendar = $this->getEntityCalendar();

        $calendar->setTitle(title: 'aa');

        $this->assertValidationErrorsCount($calendar, count: 1 ,
            expectedMessage: 'Le titre doit contenir au moins 3 caractères'
        );
    }

    /**
     * @test
     * @throws Exception
     */
    public function EntityCalendarIsNotValidLengthGreaterThan100Characters(): void
    {
        $calendar = $this->getEntityCalendar();

        $calendar->setTitle(title: str_repeat(string: 'a', times: 101));

        $this->assertValidationErrorsCount($calendar, count: 1 ,
            expectedMessage: 'Le titre doit contenir au maximum 100 caractères'
        );
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

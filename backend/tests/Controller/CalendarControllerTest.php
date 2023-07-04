<?php

namespace App\Tests\Controller;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CalendarControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CalendarRepository $repository;
    private string $path = '/calendrier/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Calendar::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request(method: 'GET', uri: $this->path);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertPageTitleContains(expectedTitle: 'Calendar index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());
        $uri = 'ajouter';

        $this->client->request(method: 'GET', uri: sprintf('%s'.$uri, $this->path));

        self::assertResponseStatusCodeSame(expectedCode: 200);

        $this->client->submitForm(button: 'Sauvegarder', fieldValues: [
            'calendar[title]' => 'Testing',
            'calendar[start]' => '2023-01-01 16:00:00',
            'calendar[end]' => '2023-01-01 16:45:00',
            'calendar[description]' => 'Testing now',
            'calendar[all_day]' => false,
            'calendar[background_color]' => '#DC134C',
            'calendar[border_color]' => '#A7123C',
            'calendar[text_color]' => '#FFFFFF',
        ]);

        self::assertResponseRedirects(expectedLocation: $this->path);

        self::assertCount(expectedCount: $originalNumObjectsInRepository + 1, haystack: $this->repository->findAll());
    }

    public function testShow(): void
    {
        $fixture = new Calendar();
        $fixture->setTitle(title: 'Calendrier');
        $fixture->setStart(start: new \DateTime(datetime: '2023-01-01 16:00:00'));
        $fixture->setEnd(end: new \DateTime(datetime: '2023-01-01 19:45:00'));
        $fixture->setDescription(description: 'My calendar description');
        $fixture->setAllDay(all_day: false);
        $fixture->setBackgroundColor(background_color: '#DC134C');
        $fixture->setBorderColor(border_color: '#A7123C');
        $fixture->setTextColor(text_color: '#FFFFFF');

        $this->repository->save(entity: $fixture, flush: true);

        $this->client->request(method: 'GET', uri: sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(expectedCode: 200);
        self::assertPageTitleContains(expectedTitle: 'Calendrier');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $fixture = new Calendar();
        $fixture->setTitle(title: 'Calendrier');
        $fixture->setStart(new \DateTime(datetime: '2023-01-01'));
        $fixture->setEnd(new \DateTime(datetime: '2023-01-01'));
        $fixture->setDescription(description: 'My calendar description');
        $fixture->setAllDay(all_day: false);
        $fixture->setBackgroundColor(background_color: '#DC134C');
        $fixture->setBorderColor(border_color: '#A7123C');
        $fixture->setTextColor(text_color: '#FFFFFF');

        $this->repository->save($fixture, true);

        $crawler = $this->client->request(method: 'GET', uri: sprintf('%s%s/modifier', $this->path, $fixture->getId()));

        $form = $crawler->selectButton(value: 'Mettre à jour')->form();
        $form['calendar[title]'] = 'Something New';
        $form['calendar[start]'] = '2025-01-01T00:00';
        $form['calendar[end]'] = '2025-01-01T00:00';
        $form['calendar[description]'] = 'Something New';
        $form['calendar[all_day]']->tick();
        $form['calendar[background_color]'] = '#DC134C';
        $form['calendar[border_color]'] = '#A7123C';
        $form['calendar[text_color]'] = '#FFFFFF';

        $this->client->submit($form);

        // dd($this->client->getResponse()->getContent());

        self::assertResponseRedirects(expectedLocation: $this->path);

        $updatedFixture = $this->repository->findAll();

        self::assertSame(expected: 'Something New', actual: $updatedFixture[0]->getTitle());
        self::assertEquals(new \DateTime(datetime: '2025-01-01'), $updatedFixture[0]->getStart());
        self::assertEquals(expected: new \DateTime(datetime: '2025-01-01'), actual: $updatedFixture[0]->getEnd());
        self::assertSame(expected: 'Something New', actual: $updatedFixture[0]->getDescription());
        self::assertTrue($updatedFixture[0]->isAllDay());
        self::assertSame(expected: '#DC134C', actual: $updatedFixture[0]->getBackgroundColor());
        self::assertSame(expected: '#A7123C', actual: $updatedFixture[0]->getBorderColor());
        self::assertSame(expected: '#FFFFFF', actual: $updatedFixture[0]->getTextColor());
    }


    public function testRemove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Calendar();
        $fixture->setTitle(title: 'Calendrier');
        $fixture->setStart(new \DateTime(datetime: '2023-01-01'));
        $fixture->setEnd(new \DateTime(datetime: '2023-01-01'));
        $fixture->setDescription(description: 'My calendar description');
        $fixture->setAllDay(all_day: false);
        $fixture->setBackgroundColor(background_color: '#DC134C');
        $fixture->setBorderColor(border_color: '#A7123C');
        $fixture->setTextColor(text_color: '#FFFFFF');

        $this->repository->save(entity: $fixture, flush: true);

        self::assertCount(expectedCount: $originalNumObjectsInRepository + 1, haystack: $this->repository->findAll());

        $this->client->request(method: 'GET', uri: sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm(button: 'Effacer');

        self::assertCount($originalNumObjectsInRepository, $this->repository->findAll());
        self::assertResponseRedirects(expectedLocation: $this->path);
    }
}

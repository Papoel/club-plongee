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

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        //self::assertPageTitleContains(expectedTitle: 'Calendar index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());
        $uri = 'ajouter';

        // Choix d'une valeur valide aléatoire parmi les choix possibles
        $validColorChoices = [
            'primary',
            'green',
            'danger',
            'yellow',
            'teal',
            'secondary',
            'dark',
            'purple',
            'pink',
            'orange',
            'blue',
            'metal',
            'white',
        ];
        $randomValidColor = $validColorChoices[array_rand($validColorChoices)];

        $this->client->request(method: 'GET', uri: sprintf('%s'.$uri, $this->path));

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);

        /*$this->client->submitForm(button: 'Sauvegarder', fieldValues: [
            'calendar[title]' => 'Testing',
            'calendar[start]' => '2023-01-01 16:00:00',
            'calendar[end]' => '2023-01-01 16:45:00',
            'calendar[description]' => 'Testing now',
            'calendar[all_day]' => false,
            'calendar[background_color]' => $randomValidColor,
            'calendar[border_color]' => $randomValidColor,
            'calendar[text_color]' => $randomValidColor,
        ]);

        self::assertResponseRedirects(expectedLocation: $this->path);

        self::assertCount(expectedCount: $originalNumObjectsInRepository + 1, haystack: $this->repository->findAll());

        // Tester avec une valeur invalide
        $this->client->request(method: 'GET', uri: $this->path . $uri);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK); */
    }

    public function testShow(): void
    {
        // Choix d'une valeur valide aléatoire parmi les choix possibles
        $validColorChoices = [
            'primary',
            'green',
            'danger',
            'yellow',
            'teal',
            'secondary',
            'dark',
            'purple',
            'pink',
            'orange',
            'blue',
            'metal',
            'white',
        ];
        $randomValidColor = $validColorChoices[array_rand($validColorChoices)];

        $fixture = new Calendar();
        $fixture->setTitle(title: 'Testing Show');
        $fixture->setStart(start: new \DateTime(datetime: '2023-01-01 16:00:00'));
        $fixture->setEnd(end: new \DateTime(datetime: '2023-01-01 19:45:00'));
        $fixture->setDescription(description: 'My calendar description');
        $fixture->setBackgroundColor(background_color: $randomValidColor);
        $fixture->setBorderColor(border_color: $randomValidColor);
        $fixture->setTextColor(text_color: $randomValidColor);

        $this->repository->save(entity: $fixture, flush: true);

        $this->client->request(method: 'GET', uri: sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        // self::assertPageTitleContains(expectedTitle: 'Testing Show');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        // Choix d'une valeur valide aléatoire parmi les choix possibles
        $validColorChoices = [
            'primary',
            'green',
            'danger',
            'yellow',
            'teal',
            'secondary',
            'dark',
            'purple',
            'pink',
            'orange',
            'blue',
            'metal',
            'white',
        ];
        $randomValidColor = $validColorChoices[array_rand($validColorChoices)];

        $fixture = new Calendar();
        $fixture->setTitle(title: 'Calendrier');
        $fixture->setStart(new \DateTime(datetime: '2023-01-01'));
        $fixture->setEnd(new \DateTime(datetime: '2023-01-01'));
        $fixture->setDescription(description: 'La description du calendrier');
        $fixture->setBackgroundColor(background_color: $randomValidColor);
        $fixture->setBorderColor(border_color: $randomValidColor);
        $fixture->setTextColor(text_color: $randomValidColor);

        $this->repository->save(entity: $fixture, flush: true);

        $crawler = $this->client->request(method: 'GET', uri: sprintf('%s%s/modifier', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        /*$form = $crawler->selectButton(value: 'Mettre à jour')->form();
        $form['calendar[title]'] = 'Mise a jour du calendrier';
        $form['calendar[start]'] = '2025-01-01T00:00';
        $form['calendar[end]'] = '2025-01-01T00:00';
        $form['calendar[description]'] = 'Ce calendrier a été mis à jour';
        $form['calendar[all_day]']->tick();
        $form['calendar[background_color]'] = $randomValidColor;
        $form['calendar[border_color]'] = $randomValidColor;
        $form['calendar[text_color]'] = $randomValidColor;

        $this->client->submit($form);

        self::assertResponseRedirects(expectedLocation: $this->path);

        $updatedFixture = $this->repository->findAll();

        self::assertSame(expected: 'Mise a jour du calendrier', actual: $updatedFixture[0]->getTitle());
        self::assertEquals(new \DateTime(datetime: '2025-01-01'), $updatedFixture[0]->getStart());
        self::assertEquals(expected: new \DateTime(datetime: '2025-01-01'), actual: $updatedFixture[0]->getEnd());
        self::assertSame(expected: 'Ce calendrier a été mis à jour', actual: $updatedFixture[0]->getDescription());
        self::assertTrue($updatedFixture[0]->isAllDay());
        self::assertSame(expected: $randomValidColor, actual: $updatedFixture[0]->getBackgroundColor());
        self::assertSame(expected: $randomValidColor, actual: $updatedFixture[0]->getBorderColor());
        self::assertSame(expected: $randomValidColor, actual: $updatedFixture[0]->getTextColor());*/
    }


    public function Remove(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        // Choix d'une valeur valide aléatoire parmi les choix possibles
        $validColorChoices = [
            '#1f96a5',
            '#007bff',
            '#28a745',
            '#dc3545',
            '#ffc107',
            '#17a2b8',
            '#e83e8c',
            '#6f42c1',
            '#6c757d',
            '#363636',
            '#ffffff'
        ];
        $randomValidColor = $validColorChoices[array_rand($validColorChoices)];

        $fixture = new Calendar();
        $fixture->setTitle(title: 'Calendrier');
        $fixture->setStart(new \DateTime(datetime: '2023-01-01'));
        $fixture->setEnd(new \DateTime(datetime: '2023-01-01'));
        $fixture->setDescription(description: 'My calendar description');
        $fixture->setBackgroundColor(background_color: $randomValidColor);
        $fixture->setBorderColor(border_color: $randomValidColor);
        $fixture->setTextColor(text_color: $randomValidColor);

        $this->repository->save(entity: $fixture, flush: true);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_UNAUTHORIZED);
        self::assertCount(expectedCount: $originalNumObjectsInRepository + 1, haystack: $this->repository->findAll());


        $this->client->request(method: 'GET', uri: sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm(button: 'Effacer');

        self::assertCount($originalNumObjectsInRepository, $this->repository->findAll());
        self::assertResponseRedirects(expectedLocation: $this->path);
    }
}

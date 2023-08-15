<?php

namespace App\Tests\Controller;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
            $this->repository->remove(entity: $object, flush: true);
        }
    }

    public function logInAsAdmin(): Crawler
    {
        // 1. Requête pour me connecter en tant qu'administrateur
        $crawler = $this->client->request(method: Request::METHOD_GET, uri: $this->path);
        // Si aucun utilisateur n'est connecté, je suis redirigé vers la page de connexion
        self::assertResponseRedirects(expectedLocation: '/connexion');
        // Suivre la redirection
        $crawler = $this->client->followRedirect();
        // Vérifier que je suis sur la page de connexion
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);

        // 2. Remplir le formulaire de connexion
        $form = $crawler->filter(selector: '#login_form')->form([
            'email' => 'papoel@admin.fr',
            'password' => 'admin1234',
        ]);

        // 3. Soumettre le formulaire
        $this->client->submit($form);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);

        // 4. Suivre la redirection pour accéder à la page d'accueil
        $this->client->followRedirect();
        self::assertRouteSame(expectedRoute: 'app_calendar_index');

        return $crawler;
    }

    public function testAccessWithoutAuthentication(): void
    {
        $this->client->request(method: Request::METHOD_GET, uri: $this->path);

        // L'utilisateur non connecté doit être redirigé vers la page de connexion
        self::assertResponseRedirects(expectedLocation: '/connexion');
    }

    public function testAdminAccess(): void
    {
        // Authentifier en tant qu'administrateur
        $crawler = $this->logInAsAdmin();

        // 5. Vérifier que j'arrive sur la page '/calendrier' avec un statut 200
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }

    public function testUserAccess(): void
    {
        // 1. Requête pour me connecter en tant qu'administrateur
        $crawler = $this->client->request(method: Request::METHOD_GET, uri: $this->path);
        // Si aucun utilisateur n'est connecté, je suis redirigé vers la page de connexion
        self::assertResponseRedirects(expectedLocation: '/connexion');
        // Suivre la redirection
        $crawler = $this->client->followRedirect();
        // Vérifier que je suis sur la page de connexion
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);

        // 2. Remplir le formulaire de connexion
        $randomUser = 'user' . random_int(min: 1, max: 5) . '@test.fr';
        $form = $crawler->filter(selector: '#login_form')->form([
            'email' => $randomUser,
            'password' => 'plongee',
        ]);

        // 3. Soumettre le formulaire
        $this->client->submit($form);
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FOUND);

        // 4. Suivre la redirection pour accéder à la page d'accueil
        $this->client->followRedirect();
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_FORBIDDEN);
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

        // 1. Connecter un administrateur
        $crawler = $this->logInAsAdmin();

        // 2. Accéder à la page de création
        $this->client->request(method: Request::METHOD_GET, uri: $this->path . $uri);

        $this->client->submitForm(button: 'Sauvegarder', fieldValues: [
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
        $this->client->request(method: Request::METHOD_GET, uri: $this->path . $uri);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }

    public function testShow(): void
    {
        // 1. Connecter un administrateur
        $this->logInAsAdmin();

        // 2. Choix d'une valeur valide aléatoire parmi les choix possibles
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

        // 3. Créer un objet avec des valeurs valides
        $fixture = new Calendar();
        $fixture->setTitle(title: 'Testing Show');
        $fixture->setStart(start: new \DateTime(datetime: '2023-01-01 16:00:00'));
        $fixture->setEnd(end: new \DateTime(datetime: '2023-01-01 19:45:00'));
        $fixture->setDescription(description: 'My calendar description');
        $fixture->setBackgroundColor(background_color: $randomValidColor);
        $fixture->setBorderColor(border_color: $randomValidColor);
        $fixture->setTextColor(text_color: $randomValidColor);

        // 4. Sauvegarder en base de données
        $this->repository->save(entity: $fixture, flush: true);

        // 5. Accéder à la page de détails
        $this->client->request(method: 'GET', uri: sprintf('%s%s', $this->path, $fixture->getId()));

        // 6. Vérifier le statut de réponse
        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
    }

    /* TODO: Tester la modification du formulaire */
    public function Edit(): void {}

    public function testRemove(): void
    {
        // 1. Connecter un administrateur
        $this->logInAsAdmin();

        // 2. Choix d'une valeur valide aléatoire parmi les choix possibles
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

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Calendar();
        $fixture->setTitle(title: 'Calendrier');
        $fixture->setStart(new \DateTime(datetime: '2023-01-01'));
        $fixture->setEnd(new \DateTime(datetime: '2023-01-01'));
        $fixture->setDescription(description: 'My calendar description');
        $fixture->setBackgroundColor(background_color: $randomValidColor);
        $fixture->setBorderColor(border_color: $randomValidColor);
        $fixture->setTextColor(text_color: $randomValidColor);

        $this->repository->save(entity: $fixture, flush: true);

        self::assertResponseStatusCodeSame(expectedCode: Response::HTTP_OK);
        self::assertCount(expectedCount: $originalNumObjectsInRepository + 1, haystack: $this->repository->findAll());


        $this->client->request(method: 'GET', uri: sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm(button: 'Effacer');

        self::assertCount($originalNumObjectsInRepository, $this->repository->findAll());
        self::assertResponseRedirects(expectedLocation: $this->path);
    }
}

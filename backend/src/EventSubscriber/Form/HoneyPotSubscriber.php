<?php

declare(strict_types=1);

namespace App\EventSubscriber\Form;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HoneyPotSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $honeyPotLogger, private readonly RequestStack $requestStack)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'checkHoneyJar',
        ];
    }

    /**
     * @description: Vérifie si les champs 'phone' et 'ville' sont remplis, si oui alors c'est un robot dans ce cas on lève une exception pour ne pas traiter le formulaire
     */
    public function checkHoneyJar(FormEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        // Récupérer les données du formulaire
        $data = $event->getData();

        // Vérifier si les champs 'ville' et 'phone' sont toujours présent dans les données du formulaire
        /* @phpstan-ignore-next-line */
        if (!array_key_exists(key: 'phone', array: $data) || !array_key_exists(key: 'ville', array: $data)) {
            throw new HttpException(statusCode: Response::HTTP_BAD_REQUEST, message: 'Tu ne peux pas soumettre ce formulaire en ayant supprimé les champs cachés ;-)');
        }

        // Déstructuring de $data pour récupérer les valeurs de 'phone' et 'ville'
        ['phone' => $phone, 'ville' => $ville] = $data;

        $ip = $request->getClientIp();

        // Vérifier si le formulaire a été soumis et s'il est valide
        if ('' !== $phone || '' !== $ville) {
            $this->honeyPotLogger->info(
                message: "Formulaire soumis par un robot. L'adresse IP est : ".$ip." | Le champ phone contient '".$phone."' | Le champ ville contient '".$ville."'",
            );
            throw new HttpException(statusCode: Response::HTTP_BAD_REQUEST, message: 'Petit robot, tu ne passeras pas !');
        }
    }
}

<?php

declare(strict_types=1);

namespace App\EventSubscriber\Form;

use App\Entity\Certificate;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CertificateExpirationDateOnLastDayOfYear implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    public function onSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $entity = $form->getData();

        // Vérifier l'instance de l'entité est bien Certificate
        if (!$entity instanceof Certificate) {
            return;
        }

        // Récupérer la date d'expiration passée dans le formulaire
        $expireAt = $entity->getExpireAt();

        // Vérifier si la date d'expiration n'est pas nulle
        if (null !== $expireAt) {
            // Changer la date définie par l'utilisateur en date du dernier jour de l'année
            $expireAt->setDate((int) $expireAt->format(format: 'Y'), month: 12, day: 31);

            // Mettre à jour la valeur de la date d'expiration dans l'entité
            $entity->setExpireAt($expireAt);
        }
    }
}

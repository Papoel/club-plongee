<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    public function __construct(private MailerInterface $mailer, private RequestStack $requestStack)
    {
    }

    /** @param array<string, Contact> $context */
    public function sendEmail(string $from, string $to, ?string $subject, string $template, array $context): void
    {
        $email = (new TemplatedEmail())
            ->from(from: $from)
            ->to(to: $to)
            ->subject(subject: $subject ?? 'Pas de sujet pour ce message.')
            ->htmlTemplate(template: $template)
            ->context(context: $context);

        try {
            $this->mailer->send(message: $email);
            /* @phpstan-ignore-next-line */
            $this->requestStack->getSession()->getFlashBag()->add(type: 'success', message: 'Votre message a bien été envoyé.');
        } catch (TransportExceptionInterface $e) {
            /* @phpstan-ignore-next-line */
            $this->requestStack->getSession()->getFlashBag()->add(type: 'danger', message: 'Une erreur est survenue lors de l\'envoi de votre message.');
        }
    }
}

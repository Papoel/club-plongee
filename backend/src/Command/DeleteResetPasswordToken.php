<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Supprimer les tokens de réinitialisation de mot de passe qui ont été demandés il y a plus de 24 heures.
 * Cette commande est à exécuter tous les jours à 03H00.
 * Un Email est envoyé à l'utilisateur pour l'avertir que sa demande de réinitialisation de mot de passe a expiré.
 */
class DeleteResetPasswordToken extends Command
{
    public const CMD_NAME = 'app:delete-reset-password-token';

    private SymfonyStyle $io;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerService $mailerService,
    ) {
        parent::__construct(name: self::CMD_NAME);
    }

    public function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Suppression des tokens de réinitialisation de mot de passe');

        $users = $this->userRepository->findAll();
        $count = 0;
        $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
        $today = new \DateTimeImmutable(datetime: 'now', timezone: $timezone);

        foreach ($users as $user) {
            // Vérifier si le token de réinitialisation de mot de passe a été demandé il y a plus de 24 heures
            if ($user->getResetTokenRequestedAt() instanceof \DateTimeImmutable) {
                // Calculer le nombre d'heures entre aujourd'hui et la date de demande de réinitialisation de mot de passe
                // Ajouter 2 heures pour prendre en compte le décalage horaire
                $diff = date_diff(
                    baseObject: $today->add(interval: new \DateInterval(duration: 'PT2H')),
                    targetObject: $user->getResetTokenRequestedAt(),
                    absolute: true
                );

                // Si le nombre de jours est supérieur ou égal à 1, on envoie un email à l'utilisateur pour l'avertir puis on supprime le token
                if ($diff->days >= 1) {
                    // Envoyer un email à l'utilisateur pour l'avertir que sa demande de réinitialisation de mot de passe a expiré
                    $this->mailerService->sendEmail(
                        from: 'no-reply@club-plongee-maubeugeois.fr',
                        /* @var User $user */
                        to: $user->getEmail(),
                        subject: 'Expiration de votre demande de réinitialisation de mot de passe',
                        template: 'emails/reset_password_expired.html.twig',
                        context: ['user' => $user]
                    );

                    $user->setResetToken(resetToken: null);
                    $user->setResetTokenRequestedAt(reset_token_requested_at: null);
                    $this->entityManager->flush();
                    ++$count;
                }
            }
        }

        if (0 === $count) {
            $this->io->warning(message: 'Aucun Token de réinitialisation de mot de passe supérieur à 24 heures trouvé.');
        } else {
            $this->io->success(message: sprintf('Suppression de %d token(s) de réinitialisation de mot de passe', $count));
        }

        return self::SUCCESS;
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\Account\ResetPasswordRequestType;
use App\Form\Account\ResetPasswordType;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetPassword extends AbstractController
{
    #[Route('/mot-de-passe-oublie', name: 'security_reset_password')]
    public function resetPasswordRequest(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        MailerService $mailerService
    ): Response {
        $form = $this->createForm(type: ResetPasswordRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Rechercher l'utilisateur avec l'email renseigné
            $formData = $form->getData();
            if (is_array($formData) && array_key_exists('email', $formData)) {
                /** @var User $user */
                $user = $userRepository->findOneBy(['email' => $formData['email']]);
            } else {
                $this->addFlash(type: 'danger', message: 'Une erreur est survenue veuillez réessayer.');

                return $this->redirectToRoute(route: 'app_login');
            }

            /* @phpstan-ignore-next-line */
            if ($user) {
                $token = $tokenGenerator->generateToken();
                try {
                    $user->setResetToken(resetToken: $token);
                    $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
                    $user->setResetTokenRequestedAt(reset_token_requested_at: new \DateTimeImmutable(datetime: 'now', timezone: $timezone));
                    $entityManager->flush();

                    // Générer un lien de réinitialisation de mot de passe
                    $url = $this->generateUrl(
                        route: 'security_change_password',
                        parameters: ['token' => $token],
                        referenceType: UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    // Envoyer un email à l'utilisateur
                    $mailerService->sendEmail(
                        from: 'no-reply@club-plongee-maubeugeois.fr',
                        to: $user->getEmail(),
                        subject: 'Réinitialisation de votre mot de passe',
                        template: 'emails/reset_password.html.twig',
                        context: [
                            'user' => $user,
                            'url' => $url,
                        ]
                    );

                    $this->addFlash(type: 'success', message: 'Un email de réinitialisation de mot de passe vous a été envoyé.');

                    return $this->redirectToRoute(route: 'app_login');
                } catch (\Exception $e) {
                    $this->addFlash(type: 'warning', message: 'Une erreur est survenue : '.$e->getMessage());

                    return $this->redirectToRoute(route: 'app_login');
                }
            }
        }

        return $this->render(view: 'security/reset_password_request.html.twig', parameters: [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modifier-mot-de-passe/{token}', name: 'security_change_password')]
    public function changePassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // Verification du token
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        // 1. Le Token existe
        if ($user) {
            // Création du formulaire
            $form = $this->createForm(type: ResetPasswordType::class);
            $form->handleRequest($request);

            // 2. Le formulaire est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                // Suppression du token
                $user->setResetToken(resetToken: null);
                $user->setResetTokenRequestedAt(reset_token_requested_at: null);

                // Hashage du mot de passe
                $formData = $form->getData();

                if (is_array($formData) && array_key_exists('password', $formData)) {
                    $formPassword = $formData['password'];
                    $user->setPassword(
                        password: $passwordHasher
                            ->hashPassword(
                                user: $user,
                                plainPassword: $formPassword
                            )
                    );
                } else {
                    $this->addFlash(type: 'danger', message: 'Une erreur est survenue veuillez réessayer.');

                    return $this->redirectToRoute(route: 'app_login');
                }

                // Enregistrement en base de données
                $entityManager->flush();

                // Message flash
                $this->addFlash(type: 'success', message: 'Votre mot de passe a bien été modifié.');

                return $this->redirectToRoute(route: 'app_login');
            }

            return $this->render(view: 'security/change_password.html.twig', parameters: [
                'form' => $form->createView(),
            ]);
        }

        $this->addFlash(type: 'danger', message: 'Le token est inconnu ou a expiré.');

        return $this->redirectToRoute(route: 'app_login');
    }
}

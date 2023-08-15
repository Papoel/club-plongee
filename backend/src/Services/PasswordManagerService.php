<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordManagerService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly FormFactoryInterface $formFactory,
        protected RequestStack $requestStack,
    ) {
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): Response
    {
        // Vérification du mot de passe actuel
        if (!$this->hasher->isPasswordValid($user, $currentPassword)) {
            $form = $this->formFactory->create(type: ChangePasswordType::class);

            $errorMessage = 'Le mot de passe actuel est incorrect.';
            $formError = new FormError($errorMessage);

            $form->get('currentPassword')->addError($formError);

            // Message flash d'erreur
            /* @phpstan-ignore-next-line */
            $this->requestStack->getSession()->getFlashBag()->add(type: 'danger', message: $errorMessage);

            return new RedirectResponse(url: $this->urlGenerator->generate(name: 'account_settings'));
        }

        // Modification du mot de passe
        $hashedPassword = $this->hasher->hashPassword(user: $user, plainPassword: $newPassword);
        $user->setPassword($hashedPassword);

        // Enregistrement en base de données
        $this->userRepository->save(entity: $user, flush: true);

        // Message flash de confirmation
        /* @phpstan-ignore-next-line */
        $this->requestStack->getSession()->getFlashBag()->add(type: 'success', message: 'Votre mot de passe a bien été modifié, déconnectez-vous puis reconnectez-vous pour appliquer les modifications.');

        // Redirection vers la page de profil
        // déconnecter l'utilisateur puis le rediriger vers la page de connexion
        return new RedirectResponse(url: $this->urlGenerator->generate(name: 'account_settings'));
    }

    public function getChangePasswordForm(User $user): FormInterface
    {
        return $this->formFactory->create(type: ChangePasswordType::class, data: $user);
    }
}

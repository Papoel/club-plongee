<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\BasicInfoType;
use App\Form\DeleteAccountType;
use App\Repository\UserRepository;
use App\Services\PasswordManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADHERENT')]
#[Route(path: '/mon-compte', name: 'account_')]
class AccountController extends AbstractController
{
    #[Route(path: '/', name: 'overview')]
    public function accountOverview(): Response
    {
        return $this->render(view: 'pages/account/account-overview.html.twig');
    }

    #[Route(path: '/profil', name: 'settings')]
    public function accountSettings(
        Request $request,
        PasswordManagerService $passwordManager,
        EntityManagerInterface $entityManager,
        UserRepository $userRepostitory,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        // Récupération du formulaire de modification du mot de passe à partir du service
        $form = $passwordManager->getChangePasswordForm($user);
        // Récupération du formulaire de modification des informations de base
        $formBasicInfo = $this->createForm(type: BasicInfoType::class, data: $user);
        // Récupération du formulaire de suppression de compte
        $deleteForm = $this->createForm(type: DeleteAccountType::class, data: $user);

        $form->handleRequest($request);
        $formBasicInfo->handleRequest($request);
        $deleteForm->handleRequest($request);

        // Validation du formulaire de modification des informations de base
        if ($formBasicInfo->isSubmitted() && $formBasicInfo->isValid()) {
            // Vérifier que l'utilisateur a bien effectué au moins une modification pour éviter de mettre à jour la base de données inutilement

            // Mettre à jour les informations de base de l'utilisateur
            $this->addFlash(type: 'success', message: 'Vos informations ont bien été modifiées');
            $entityManager->flush();
        } elseif ($formBasicInfo->isSubmitted() && !$formBasicInfo->isValid()) {
            $this->addFlash(type: 'info', message: 'Vos informations n\'ont pas pu être modifiées');
        }

        // Validation du formulaire de modification du mot de passe
        if ($form->isSubmitted() && $form->isValid()) {
            $currentPasswordField = $form->get('currentPassword');
            $newPasswordField = $form->get('newPassword')['first'];

            if ($newPasswordField instanceof FormInterface) {
                if ($currentPasswordField->isValid()) {
                    $currentPasswordData = $currentPasswordField->getData();

                    // Vérifier si le champ current password est de type chaîne
                    if (is_string($currentPasswordData)) {
                        $currentPassword = $currentPasswordData;

                        // Vérifier si le champ new password a été rempli
                        $newPasswordData = $newPasswordField->getData();
                        if (is_string($newPasswordData)) {
                            $newPassword = $newPasswordData;

                            return $passwordManager->changePassword(user: $user, currentPassword: $currentPassword, newPassword: $newPassword);
                        }

                        // Gérer le cas où le champ new password n'a pas été rempli ou n'est pas de type chaîne
                        $this->addFlash(type: 'danger', message: 'Le champ de nouveau mot de passe n\'a pas été correctement rempli.');
                    } else {
                        // Gérer le cas où le champ current password n'est pas de type chaîne
                        $this->addFlash(type: 'danger', message: 'Le champ de mot de passe actuel n\'est pas valide.');
                    }
                } else {
                    $this->addFlash(type: 'danger', message: 'Le formulaire de mot de passe contient des erreurs.');
                }
            } else {
                $this->addFlash(type: 'danger', message: 'Les champs de mot de passe n\'existent pas.');
            }
        }

        // Validation du formulaire de suppression de compte
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            // Passer le compte en inactif
            $user->setIsActive(isActive: false);
            // Ajouter la date et l'heure de la demande de suppression sur le TimesZone UTC Paris
            $timezone = new \DateTimeZone(timezone: 'Europe/Paris');
            $user->setAccountDeletionRequest(account_deletion_request: new \DateTimeImmutable(timezone: $timezone));
            $entityManager->flush();
            // Flash message d'avertissement
            $this->addFlash(type: 'warning', message: 'Votre compte est désormais inactif. Votre demande de suppression sera traitée dans les plus brefs délais.');
        }

        return $this->render(view: 'pages/account/account-settings.html.twig', parameters: [
            'changePasswordForm' => $form->createView(),
            'basicInfoForm' => $formBasicInfo->createView(),
            'deleteForm' => $deleteForm->createView(),
        ]);
    }

    #[Route(path: '/cancel-deletion', name: 'cancel_deletion')]
    public function cancelDelete(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        /** @var User $user */
        if ($user->getAccountDeletionRequest()) {
            // Annuler la demande de suppression en supprimant la date de demande de suppression
            $user->setAccountDeletionRequest(account_deletion_request: null);
            $user->setIsActive(isActive: true);
            $entityManager->flush();

            $this->addFlash(type: 'success', message: 'La demande de suppression de compte a été annulée.');
        } else {
            $this->addFlash(type: 'danger', message: 'Aucune demande de suppression de compte en attente.');
        }

        return $this->redirectToRoute(route: 'account_settings');
    }
}

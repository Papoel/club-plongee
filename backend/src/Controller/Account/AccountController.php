<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Entity\Certificate;
use App\Entity\User;
use App\Form\Account\AvatarType;
use App\Form\Account\BasicInfoType;
use App\Form\Account\DeleteAccountType;
use App\Form\CertificateType;
use App\Repository\CertificateRepository;
use App\Repository\LicenceRepository;
use App\Repository\UserRepository;
use App\Services\PasswordManagerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[isGranted('ROLE_ADHERENT')]
#[Route(path: '/mon-compte/{id}', name: 'account_')]
class AccountController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route(path: '/', name: 'overview')]
    public function accountOverview(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        LicenceRepository $licenceRepository,
        CertificateRepository $certificateRepository,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        // Récupérer la Licence de l'utilisateur
        $licence = $licence = $user->getLicence();
        // Récupération des certificats médicaux
        $medicalCertificates = $certificateRepository->findBy(['user' => $user], ['expireAt' => 'DESC'], 3);

        // Créez une instance vide de Certificate pour le formulaire (ou une instance existante si nécessaire)
        $medicalCertificate = new Certificate();

        // Récupération du formulaire de gestion des certificats médicaux
        $medicalForm = $this->createForm(type: CertificateType::class, data: $medicalCertificate);
        $medicalForm->handleRequest($request);

        // Validation du formulaire de gestion des certificats médicaux
        if ($medicalForm->isSubmitted() && $medicalForm->isValid()) {
            // Récupérer le fichier
            $certificateFile = $medicalForm->get('medicalCertificateFile')->getData();

            if (null !== $certificateFile) {
                // Récupérer le type mime du fichier
                /** @phpstan-ignore-next-line */
                $certificateMimeType = $certificateFile->getMimeType();
                // Lister les types de fichiers autorisés
                $allowedMimeTypes = ['application/pdf', 'application/x-pdf'];
                // Récupérer le nom du fichier
                /** @phpstan-ignore-next-line */
                $originalFileName = $certificateFile->getClientOriginalName();

                // Vérifier si le type mime du fichier est autorisé
                if (!in_array(needle: $certificateMimeType, haystack: $allowedMimeTypes, strict: true)) {
                    $this->addFlash(
                        type: 'danger',
                        message: 'Le type de fichier n\'est pas autorisé, veuillez sélectionner un fichier au format PDF.'
                    );
                    // REDIRECTION
                    return $this->redirectToRoute(route: 'account_overview', parameters: ['id' => $user->getId()]);
                }

                // Enregistrer le certificat médical.
                /* @var UploadedFile $certificateFile */
                /* @phpstan-ignore-next-line */
                $medicalCertificate->setMedicalCertificateFile(medicalCertificateFile: $certificateFile);
                $medicalCertificate->setOriginalFileName(originalFileName: $originalFileName);
                $medicalCertificate->setUser(user: $user);

                $entityManager->persist($medicalCertificate);
                $entityManager->flush();

                $this->addFlash(type: 'success', message: 'Votre certificat médical a bien été enregistré.');

                return $this->redirectToRoute(route: 'account_overview', parameters: ['id' => $user->getId()]);
            } else {
                $this->addFlash(type: 'info', message: 'Veuillez sélectionner un fichier avant de valider.');
            }
        }

        return $this->render(view: 'pages/account/account-overview.html.twig', parameters: [
            'medicalForm' => $medicalForm->createView(),
            'medicalCertificates' => $medicalCertificates,
            'licence' => $licence,
        ]);
    }

    /**
     * @throws \Exception
     */
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
        // Récupération du formulaire d'ajout d'avatar
        $avatarForm = $this->createForm(type: AvatarType::class, data: $user);

        $form->handleRequest($request);
        $formBasicInfo->handleRequest($request);
        $deleteForm->handleRequest($request);
        $avatarForm->handleRequest($request);

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

        // Validation du formulaire d'ajout d'avatar
        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            // Récupérer le fichier
            $avatarFile = $avatarForm->get('avatarFile')->getData();

            if (null !== $avatarFile) {
                // Récupérer le type mime du fichier
                /** @phpstan-ignore-next-line */
                $avatarMimeType = $avatarFile->getMimeType();
                // Lister les types de fichiers autorisés
                $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                // Vérifier si le type mime du fichier est autorisé
                if (!in_array(needle: $avatarMimeType, haystack: $allowedMimeTypes, strict: true)) {
                    $this->addFlash(type: 'danger', message: 'Le type de fichier n\'est pas autorisé, un avatar par défaut a été appliqué.');
                    // Définir AvatarFile à null dans la base de données
                    $user->setAvatar(avatar: null);
                    $entityManager->flush();
                    // REDIRECTION
                    return $this->redirectToRoute(route: 'account_settings', parameters: ['id' => $user->getId()]);
                }

                // Enregistrer l'avatar.
                /* @var UploadedFile $avatarFile */
                /* @phpstan-ignore-next-line */
                $user->setAvatarFile(avatarFile: $avatarFile);
                $entityManager->flush();
                $this->addFlash(type: 'success', message: 'Votre avatar a bien été modifié.');
            } else {
                $this->addFlash(type: 'danger', message: 'Veuillez sélectionner un fichier avant de valider.');
            }
        }

        return $this->render(view: 'pages/account/account-settings.html.twig', parameters: [
            'changePasswordForm' => $form->createView(),
            'basicInfoForm' => $formBasicInfo->createView(),
            'deleteForm' => $deleteForm->createView(),
            'avatarForm' => $avatarForm->createView(),
        ]);
    }

    #[Route(path: '/cancel-deletion', name: 'cancel_deletion')]
    public function cancelDelete(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getAccountDeletionRequest()) {
            // Annuler la demande de suppression en supprimant la date de demande de suppression
            $user->setAccountDeletionRequest(account_deletion_request: null);
            $user->setIsActive(isActive: true);
            $entityManager->flush();

            $this->addFlash(type: 'success', message: 'La demande de suppression de compte a été annulée.');
        } else {
            $this->addFlash(type: 'danger', message: 'Aucune demande de suppression de compte en attente.');
        }

        return $this->redirectToRoute(route: 'account_settings', parameters: ['id' => $user->getId()]);
    }

    #[Route(path: '/delete-avatar', name: 'delete_avatar')]
    public function deleteAvatar(EntityManagerInterface $entityManager): Response
    {
        /* @var User|null $user */
        $user = $this->getUser();

        if ($user instanceof User) {
            // Récupérer le nom de l'avatar puis le supprimer du dossier public
            $avatar = $user->getAvatar();
            $uploadBasePath = $this->getParameter(name: 'upload_base_path');
            $avatarPath = '';

            if (null !== $uploadBasePath && null !== $avatar) {
                /** @phpstan-ignore-next-line */
                $avatarPath = sprintf('%s/%s', (string) $uploadBasePath, (string) $avatar);
                // Vérifier si le fichier existe
                if (file_exists(filename: 'assets'.$avatarPath)) {
                    // Supprimer le fichier
                    unlink(filename: 'assets'.$avatarPath);
                    // Supprimer l'avatar de la base de données
                    $user->setAvatar(avatar: null);
                    $entityManager->flush();
                    $this->addFlash(type: 'purple', message: 'Votre avatar a bien été supprimé.');
                }
            }
        } else {
            $this->addFlash(type: 'danger', message: 'Vous devez être connecté pour effectuer cette action.');

            return $this->redirectToRoute(route: 'app_login');
        }

        return $this->redirectToRoute(route: 'account_settings', parameters: ['id' => $user->getId()]);
    }

    #[Route(path: '/delete-certificate/', name: 'delete_certificate')]
    public function deleteCertificate(
        Certificate $id,
        EntityManagerInterface $entityManager,
        CertificateRepository $certificateRepository
    ): Response {
        $user = $this->getUser();

        if ($user instanceof User) {
            // Récupérer le certificat médical par son ID
            $certificate = $certificateRepository->find($id);

            if ($certificate instanceof Certificate) {
                // Vérifier si le certificat appartient à l'utilisateur connecté
                if ($certificate->getUser() === $user) {
                    // Récupérer le chemin du certificat
                    $certificatePath = $certificate->getMedicalCertificate();

                    if ($certificatePath) {
                        // Supprimer le fichier du certificat
                        $uploadBasePath = $this->getParameter(name: 'upload_medical_certificate_path');
                        /** @phpstan-ignore-next-line */
                        $fullCertificatePath = sprintf('%s/%s', $uploadBasePath, $certificatePath);

                        if (file_exists($fullCertificatePath)) {
                            unlink($fullCertificatePath);
                        }

                        // Supprimer le certificat de la base de données
                        $entityManager->remove($certificate);
                        $entityManager->flush();

                        $this->addFlash(type: 'purple', message: 'Votre certificat médical a bien été supprimé.');
                    } else {
                        $this->addFlash(type: 'danger', message: 'Le certificat médical n\'a pas été trouvé.');
                    }
                } else {
                    $this->addFlash(type: 'danger', message: 'Vous n\'êtes pas autorisé à supprimer ce certificat.');
                }
            } else {
                $this->addFlash(type: 'danger', message: 'Le certificat médical n\'a pas été trouvé.');
            }
        } else {
            $this->addFlash(type: 'danger', message: 'Vous devez être connecté pour effectuer cette action.');

            return $this->redirectToRoute(route: 'app_login');
        }

        return $this->redirectToRoute(route: 'account_overview', parameters: ['id' => $user->getId()]);
    }
}

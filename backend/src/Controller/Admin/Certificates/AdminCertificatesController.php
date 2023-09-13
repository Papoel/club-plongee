<?php

declare(strict_types=1);

namespace App\Controller\Admin\Certificates;

use App\Entity\User;
use App\Repository\CertificateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCertificatesController extends AbstractController
{
    #[Route(path: '/admin/certificates/', name: 'admin_certificates')]
    public function show(CertificateRepository $certificateRepository): Response
    {
        $certificates = $certificateRepository->findAll();

        // Vérifier si l'utilisateur est connecté et a le rôle ROLE_ADMIN
        $this->denyAccessUnlessGranted(attribute: 'ROLE_ADMIN');

        /** @phpstan-ignore-next-line */
        $directory = $this->getParameter(name: 'kernel.project_dir').'/public/assets/uploads/medical_certificate';

        // Utiliser Finder pour lister les fichiers dans le dossier
        $finder = new Finder();
        $finder->in($directory);

        $files = [];
        foreach ($finder->files() as $file) {
            $files[] = $file->getRelativePathname();
        }

        // Rechercher les fichiers manquants
        $missingFiles = [];
        foreach ($certificates as $certificate) {
            $certificateFileName = $certificate->getMedicalCertificate(); // Assurez-vous d'adapter cela à votre modèle
            if (!in_array(needle: $certificateFileName, haystack: $files, strict: true)) {
                $missingFiles[] = [
                    /** @var User $user */
                    $user = $certificate->getUser(),
                    'user' => $user->getFullname(),
                    'certificateFileName' => $certificateFileName,
                ];
            }
        }

        // Afficher un message dans la vue si des fichiers manquent
        $message = '';
        if (!empty($missingFiles)) {
            $message = 'Il semble avoir un problème avec les certificats médicaux ci-dessous.
                Veuillez contactez les utilisateurs pour qu\'ils vous envoient leurs certificats médicaux.';
        }

        return $this->render(view: 'admin/certificates/certificate.html.twig', parameters: [
            'certificates' => $certificates,
            'message' => $message,
            'missingFiles' => $missingFiles,
        ]);
    }
}

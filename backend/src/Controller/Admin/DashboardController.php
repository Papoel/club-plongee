<?php

namespace App\Controller\Admin;

use App\Entity\Certificate;
use App\Entity\Contact;
use App\Entity\Licence;
use App\Entity\User;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly UserRepository $userRepository,
        private readonly ContactRepository $contactRepository
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(crudControllerFqcn: UserCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(title: 'La plongée Maubeugeoise');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl(label: 'Site web', icon: 'fa fa-globe', url: '/');

        // Adhérents
        $totalUsers = $this->userRepository->count([]);
        yield MenuItem::section(label: 'Utilisateurs', icon: 'fa fa-users')
            ->setBadge(content: $totalUsers, style: 'success');
        yield MenuItem::subMenu(label: 'Adhérents', icon: 'fa fa-user')
            ->setSubItems(subItems: [
                MenuItem::linkToCrud(label: 'Liste des adhérents', icon: 'fa fa-list', entityFqcn: User::class),
            ]);

        // Licences
        yield MenuItem::section(label: 'Licences', icon: 'fa fa-id-card');
        yield MenuItem::subMenu(label: 'Licences adhérents', icon: 'fa fa-user')
            ->setSubItems(subItems: [
                MenuItem::linkToCrud(label: 'Liste des licences', icon: 'fa fa-list', entityFqcn: Licence::class),
            ]);

        // Certificats Médicaux
        yield MenuItem::section(label: 'Certificats');
        yield MenuItem::subMenu(label: 'Certificats médicaux', icon: 'fa fa-file')
            ->setSubItems(subItems: [
                MenuItem::linkToCrud(
                    label: 'Liste des certificats',
                    icon: 'fa fa-list',
                    entityFqcn: Certificate::class
                )
                ->setAction(actionName: Crud::PAGE_INDEX),
                MenuItem::linkToRoute(
                    label: 'Consulter',
                    icon: 'fa fa-check',
                    routeName: 'admin_certificates',
                ),
            ])
        ;

        // Contact
        $totalMessages = $this->contactRepository->count([]);
        yield MenuItem::section(label: 'Messages', icon: 'fas fa-envelope')
            ->setBadge($totalMessages, style: 'dark')
        ;
        yield MenuItem::subMenu(label: 'Action', icon: 'fas fa-bars')->setSubItems(subItems: [
            MenuItem::linkToCrud(label: 'Les messages', icon: 'fas fa-eye', entityFqcn: Contact::class),
        ]);

        // Calendrier
        yield MenuItem::section(label: 'Calendrier');

        // Déconnexion
        yield MenuItem::section();
        yield MenuItem::linkToLogout(label: 'Déconnexion', icon: 'fa fa-sign-out text-danger')
            ->setCssClass(cssClass: 'text-danger')
        ;
    }
}

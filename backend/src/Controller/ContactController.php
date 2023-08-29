<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactType;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $manager,
        UserRepository $userRepository,
        MailerService $mailer
    ): Response {
        $contact = new Contact();
        $userConnected = $this->getUser();
        if ($userConnected instanceof User) {
            $userIdentifier = $userConnected->getUserIdentifier();
        }

        if ($userConnected instanceof User) {
            $contact->setFullname($userConnected->getFullname());
            $contact->setEmail($userConnected->getEmail());
        }

        $form = $this->createForm(type: ContactType::class, data: $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            if ($contact instanceof Contact) {
                $manager->persist($contact);
                $manager->flush();

                $mailer->sendEmail(
                    from: $contact->getEmail(),
                    to: 'no-reply@club-plongee-maubeugeois.fr',
                    subject: $contact->getSubject(),
                    template: 'emails/contact.html.twig',
                    context: compact(var_name: 'contact')
                );

                $this->addFlash(type: 'success', message: 'Votre message a bien été envoyé.');

                return $this->redirectToRoute(route: 'app_contact');
            }
        }

        return $this->render(view: 'pages/contact/index.html.twig', parameters: [
            'form' => $form->createView(),
        ]);
    }
}

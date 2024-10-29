<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class DefaultController extends AbstractController
{

    #[Route('/',  name: 'app_default',)]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/contact', methods:['GET', 'POST'],  name: 'contact',)]
    public function contact(MailerInterface $mailer, Request $request): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

       if ($contactForm->isSubmitted() && $contactForm->isValid()) {

            try{
                $data = $contactForm->getData();
            
                $email = (new Email())
                    ->from($data['email'])
                    ->to('emile00013@laposte.net')
                    ->subject($data['objet'])
                    ->text($data['message']);

                $res = $mailer->send($email);
                $this->addFlash('success', 'Votre messae a bien été envoyé.');

            } catch (TransportExceptionInterface  $expection) {

                $this->addFlash('danger', 'Erreur : '. $expection->getMessage());

            }
        }

        return $this->render('default/contact.html.twig', [
            'controller_name' => 'Contact',
            'contact_form' => $contactForm->createView()
        ]);
    }
}

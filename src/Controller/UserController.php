<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
 
class UserController extends AbstractController
{

    #[Route('/login', methods:'GET', name: 'app_connexion')]
    public function connexion(): Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/inscription', methods: ['GET', 'POST'], name: 'sign_up')]
    public function signup(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->render('security/sign.html.twig',  
                [
                    'form' => $form->createView(),
                    'controller_name' => 'Vérification de l\' email',
                    'message' => [
                        'header' => 'Information',
                        'body' => 'Votre compte a bien été créé',
                        'style' => 'success'
                    ],
                    
                    
                ]);

        }

        return $this->render('security/sign.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/user',  methods:'POST', name: 'create_user')]
    public function createUser(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {


        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        
        $content = $request->getContent();
        $arrayUser = json_decode($content, true);
        $user = new User($arrayUser);

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword());

        $user->setPassword($hashedPassword);

        $jsonUser = $serializer->serialize($user, 'json');
        $response = new Response();
    

        $userRepository->add($user, true);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($jsonUser);

        return $response;

    }
}

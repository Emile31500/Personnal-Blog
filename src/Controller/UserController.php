<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
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

    #[Route('/inscription',  name: 'sign_up')]
    public function signup(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            return $this->render('registration/sign.html.twig',  
                [
                    'controller_name' => 'Vérification de l\' email',
                    'message' => [
                        'header' => 'Information',
                        'body' => 'Pour pouvoir vous authentifier, cliquez sur lien envoyé par email pour vérifier votre compte.',
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

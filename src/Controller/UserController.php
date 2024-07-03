<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
 
class UserController extends AbstractController
{
    /**
     * @Route("/api/user", methods="POST", name="create_user")
     */
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

<?php

namespace App\Controller;

use App\Entity\User;
use App\Utils\MyFunctions;
use Cocur\Slugify\Slugify;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** @Route("/post", methods={"POST"}) */
class PostController extends AbstractController {

    /** @Route("/user/create") */
    public function userCreate(Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, SerializerInterface $serializer, ObjectManager $manager) {
        $recievedData = json_decode($request->getContent(), true);
        
        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['email', 'username', 'password', 'passwordConfirm'], $recievedData);

        if (count($errors) == 0) {
            if ($recievedData['password'] == $recievedData['passwordConfirm']) {
                $slugify = new Slugify();
                $user = new User();
    
                $user
                    ->setEmail($recievedData['email'])
                    ->setUsername($recievedData['username'])
                    ->setPassword($recievedData['password'])
                    ->setSlug($slugify->slugify($recievedData['username']))
                ;
    
                // On vérifie les contraintes de validation
                $violations = $validator->validate($user);

                if (count($violations) > 0) {
                    $response = new JsonResponse();
                    $response->setContent($serializer->serialize($violations, 'json'));
                    return $response;
                } else {
                    $user->setPassword($encoder->encodePassword($user, $recievedData['password']));
                    $manager->persist($user);
                    $manager->flush();
                    $response = new JsonResponse();
                    $response->setContent($serializer->serialize($user, 'json', ['groups' => 'user']));
                    return $response;
                }

            } else {
                return new Response("Vos mots de passes ne sont pas identiques.", Response::HTTP_PRECONDITION_FAILED);
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($errors, 'json'));
            return $response;
        }
    }

    /** @Route("/user/login") */
    public function userLogin(Request $request, UserRepository $userRepository, SerializerInterface $serializer) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['email', 'password'], $recievedData);

        if (count($errors) == 0) {
            if ($user = $userRepository->findOneByEmail($recievedData['email'])) {
                if (password_verify($recievedData['password'], $user->getPassword())) {
                    $response = new JsonResponse();
                    $response->setContent($serializer->serialize($user, 'json', ['groups' => 'user']));
                    return $response;
                } else {
                    return new Response("Mot de passe incorrect.", Response::HTTP_UNAUTHORIZED);
                }
            } else {
                return new Response("Adresse email ou pseudo non trouvé.", Response::HTTP_PRECONDITION_FAILED);
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($errors, 'json'));
            return $response;
        }
    }
}

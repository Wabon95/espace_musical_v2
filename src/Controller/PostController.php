<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Event;
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


// TODO: Renvoie de texte d'erreur 

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
                    ->setSlug($slugify->slugify($recievedData['username']))
                    ->setPassword($recievedData['password'])
                ;
    
                // On vérifie les contraintes de validation
                $violations = $validator->validate($user);

                if (count($violations) > 0) {
                    $response = new JsonResponse();
                    $response
                        ->setContent($serializer->serialize($violations, 'json'))
                        ->setStatusCode(400, "Une erreur est survenue.")
                    ;
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
                $contentToReturn = $myFunctions->returnErrorMessage("Vos mots de passes ne sont pas identiques.");
                $response = new JsonResponse();
                $response
                    ->setContent($contentToReturn)
                    ->setStatusCode(400, "Une erreur est survenue.")
                ;
                return $response;
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response
                ->setContent($serializer->serialize($errors, 'json'))
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
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
                    $contentToReturn = $myFunctions->returnErrorMessage("Mot de passe incorrect.");
                    $response = new JsonResponse();
                    $response
                        ->setContent($contentToReturn)
                        ->setStatusCode(400, "Une erreur est survenue.")
                    ;
                    return $response;
                }
            } else {
                $contentToReturn = $myFunctions->returnErrorMessage("Adresse email non trouvée.");
                $response = new JsonResponse();
                $response
                    ->setContent($contentToReturn)
                    ->setStatusCode(400, "Une erreur est survenue.")
                ;
                return $response;
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response
                ->setContent($serializer->serialize($errors, 'json'))
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }

    /** @Route("/ad/create") */
    public function adCreate(Request $request, ValidatorInterface $validator, UserRepository $userRepository, SerializerInterface $serializer, ObjectManager $manager) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['author', 'title', 'content', 'type', 'price', 'pictures'], $recievedData);

        if (count($errors) == 0) {
            if ($user = $userRepository->find($recievedData['author'])) {
                $slugify = new Slugify();
                $ad = new Ad();
                $ad
                    ->setTitle($recievedData['title'])
                    ->setContent($recievedData['content'])
                    ->setType($recievedData['type'])
                    ->setAuthor($user)
                    ->setSlug($slugify->slugify($recievedData['title']))
                ;
                if ($recievedData['price'] != '') $ad->setPrice($recievedData['price']);
                if (count($recievedData['pictures']) > 0) $ad->setPictures($recievedData['pictures']);
            } else {
                $contentToReturn = $myFunctions->returnErrorMessage("Aucun utilisateur avec cet id présent en base de données.");
                $response = new JsonResponse();
                $response
                    ->setContent($contentToReturn)
                    ->setStatusCode(400, "Une erreur est survenue.")
                ;
                return $response;
            }

            $violations = $validator->validate($ad);

            if (count($violations) > 0) {
                $response = new JsonResponse();
                $response
                    ->setContent($serializer->serialize($violations, 'json'))
                    ->setStatusCode(400, "Une erreur est survenue.")
                ;
                return $response;
            } else {
                $manager->persist($ad);
                $manager->flush();
                $response = new JsonResponse();
                $response->setContent($serializer->serialize($ad, 'json', ['groups' => 'ad']));
                return $response;
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response
                ->setContent($serializer->serialize($errors, 'json'))
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }

    /** @Route("/event/create") */
    public function eventCreate(Request $request, ValidatorInterface $validator, UserRepository $userRepository, SerializerInterface $serializer, ObjectManager $manager) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['type', 'title', 'description', 'location', 'start_date', 'end_date', 'artists', 'price', 'pictures', 'author'], $recievedData);

        if (count($errors) == 0) {
            if ($user = $userRepository->find($recievedData['author'])) {
                $slugify = new Slugify();
                $event = new Event();
                $myFunctions = new MyFunctions();

                $event
                    ->setType($recievedData['type'])
                    ->setTitle($recievedData['title'])
                    ->setSlug($slugify->slugify($recievedData['title']))
                    ->setDescription($recievedData['description'])
                    ->setLocation($recievedData['location'])
                    ->setArtists($recievedData['artists'])
                    ->setStartDate($myFunctions->timestampToDatetime($recievedData['start_date']))
                    ->setEndDate($myFunctions->timestampToDatetime($recievedData['end_date']))
                    ->setAuthor($user)
                ;
                if ($recievedData['price'] != '') $event->setPrice($recievedData['price']);
                if (count($recievedData['pictures']) > 0) $event->setPictures($recievedData['pictures']);

                $violations = $validator->validate($event);

                if (count($violations) > 0) {
                    $response = new JsonResponse();
                    $response
                        ->setContent($serializer->serialize($violations, 'json'))
                        ->setStatusCode(400, "Une erreur est survenue.")
                    ;
                    return $response;
                } else {
                    $manager->persist($event);
                    $manager->flush();
                    $response = new JsonResponse();
                    $response->setContent($serializer->serialize($event, 'json', ['groups' => 'event']));
                    return $response;
                }
            } else {
                $contentToReturn = $myFunctions->returnErrorMessage("Aucun utilisateur avec cet id présent en base de données.");
                $response = new JsonResponse();
                $response
                    ->setContent($contentToReturn)
                    ->setStatusCode(400, "Une erreur est survenue.")
                ;
                return $response;
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response
                ->setContent($serializer->serialize($errors, 'json'))
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }
}

<?php

namespace App\Controller;

use App\Utils\MyFunctions;
use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** @Route("/put", methods={"PUT"}) */
class PutController extends AbstractController {

    /** @Route("/user/{slug}") */
    public function editUser(String $slug, Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, SerializerInterface $serializer, ObjectManager $manager, UserRepository $userRepository) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['userId', 'emailNew', 'username', 'firstname', 'lastname', 'address', 'picture', 'instruments', 'currentPassword', 'newPassword', 'newPasswordConfirm'], $recievedData);

        if (count($errors) == 0) {
            if ($user = $userRepository->findOneBySlug($slug)) {
                if ($recievedData['userId'] == $user->getId()) {
                    if (password_verify($recievedData['currentPassword'], $user->getPassword())) {
                        $passwordHasModified = false;
    
                        if ($recievedData['emailNew'] != $user->getEmail()) {
                            $user->setEmail($recievedData['emailNew']);
                        }
    
                        if ($recievedData['username'] != $user->getUsername()) {
                            $slugify = new Slugify();
                            $user
                                ->setUsername($recievedData['username'])
                                ->setSlug($slugify->slugify($recievedData['username']))    
                            ;
                        }
    
                        if ($recievedData['firstname'] != $user->getFirstname()) {
                            $user->setFirstname($recievedData['firstname']);
                        }
    
                        if ($recievedData['lastname'] != $user->getLastname()) {
                            $user->setLastname($recievedData['lastname']);
                        }
    
                        if ($recievedData['address'] != $user->getAddress()) {
                            $user->setAddress($recievedData['address']);
                        }
                        
                        if ($recievedData['picture'] != $user->getPicture()) {
                            $user->setPicture($recievedData['picture']);
                        }
    
                        if ($recievedData['instruments'] != $user->getInstruments()) {
                            $user->setInstruments($recievedData['instruments']);
                        }
    
                        if ($recievedData['newPassword'] != '' || $recievedData['newPasswordConfirm'] != '') {
                            if ($recievedData['newPassword'] === $recievedData['newPasswordConfirm']) {
                                if (!password_verify($recievedData['newPassword'], $user->getPassword())) {
                                    $user->setPassword($recievedData['newPassword']);
                                    $passwordHasModified = true;
                                } else {
                                    return new Response("Le nouveau mot de passe est identique à l'ancien.", Response::HTTP_PRECONDITION_FAILED);
                                }
                            } else {
                                return new Response("Les champs correspondant au nouveau mot de passe ne correspondent pas.", Response::HTTP_PRECONDITION_FAILED);
                            }
                        }
    
                        $violations = $validator->validate($user);
    
                        if (count($violations) > 0) {
                            $response = new JsonResponse();
                            $response->setContent($serializer->serialize($violations, 'json'));
                            return $response;
                        } else {
                            if ($passwordHasModified) $user->setPassword($encoder->encodePassword($user, $recievedData['newPassword']));
                            $user->setUpdatedAt(new \DateTime());
                            $manager->persist($user);
                            $manager->flush();
                            $response = new JsonResponse();
                            $response->setContent($serializer->serialize($user, 'json', ['groups' => 'user']));
                            return $response;
                        }
                    } else {
                        return new Response("Le mot de passe actuel est incorrect.", Response::HTTP_UNAUTHORIZED);
                    }
                } else {
                    return new Response("L'id fourni dans le Json ne correspond pas à celui de l'utilisateur possédant ce slug.", Response::HTTP_UNAUTHORIZED);
                }
            } else {
                return new Response("L'utilisateur demandé n'a pas été trouvé en base de données.", Response::HTTP_NOT_FOUND);
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($errors, 'json'));
            return $response;
        }
    }

    /** @Route("/ad/{slug}") */
    public function adEdit(String $slug, Request $request, ValidatorInterface $validator, SerializerInterface $serializer, ObjectManager $manager, AdRepository $adRepository, UserRepository $userRepository) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['author', 'title', 'content', 'type', 'price', 'pictures'], $recievedData);

        if (count($errors) == 0) {
            if ($ad = $adRepository->findOneBySlug($slug)) {
                if ($ad->getAuthor()->getId() == $recievedData['author']) {

                    if ($recievedData['title'] != $ad->getTitle()) {
                        $slugify = new Slugify();
                        $ad
                            ->setTitle($recievedData['title'])
                            ->setSlug($slugify->slugify($recievedData['title']))    
                        ;
                    }

                    if ($recievedData['content'] != $ad->getContent()) {
                        $ad->setContent($recievedData['content']);
                    }

                    if ($recievedData['type'] != $ad->getType()) {
                        $ad->setType($recievedData['type']);
                    }

                    if ($recievedData['price'] != $ad->getPrice()) {
                        $ad->setPrice($recievedData['price']);
                    }

                    if ($recievedData['pictures'] != $ad->getPictures()) {
                        $ad->setPictures($recievedData['pictures']);
                    }

                    $violations = $validator->validate($ad);

                    if (count($violations) > 0) {
                        $response = new JsonResponse();
                        $response->setContent($serializer->serialize($violations, 'json'));
                        return $response;
                    } else {
                        $manager->persist($ad);
                        $manager->flush();
                        $response = new JsonResponse();
                        $response->setContent($serializer->serialize($ad, 'json', ['groups' => 'ad']));
                        return $response;
                    }
                } else {
                    return new Response("Seul l'auteur est autorisé à modifier son annonce.", Response::HTTP_UNAUTHORIZED);
                }
            } else {
                return new Response("L'annonce demandée n'a pas été trouvée en base de données.", Response::HTTP_NOT_FOUND);
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($errors, 'json'));
            return $response;
        }
    }

    /** @Route("/event/{slug}") */
    public function eventEdit(String $slug, Request $request, ValidatorInterface $validator, SerializerInterface $serializer, EventRepository $eventRepository, ObjectManager $manager) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['author', 'title', 'type', 'price', 'pictures', 'start_date', 'end_date', 'location', 'artists', 'description'], $recievedData);
        
        if (count($errors) == 0) {
            if ($event = $eventRepository->findOneBySlug($slug)) {
                if ($event->getAuthor()->getId() == $recievedData['author']) {

                    if ($recievedData['title'] != $event->getTitle()) {
                        $slugify = new Slugify();
                        $event
                            ->setTitle($recievedData['title'])
                            ->setSlug($slugify->slugify($recievedData['title']))    
                        ;
                    }

                    if ($recievedData['type'] != $event->getType()) {
                        $event->setType($recievedData['type']);
                    }

                    if ($recievedData['location'] != $event->getLocation()) {
                        $event->setLocation($recievedData['location']);
                    }

                    if ($recievedData['artists'] != $event->getArtists()) {
                        $event->setArtists($recievedData['artists']);
                    }

                    if ($recievedData['description'] != $event->getDescription()) {
                        $event->setDescription($recievedData['description']);
                    }

                    if ($recievedData['price'] != $event->getPrice()) {
                        $event->setPrice($recievedData['price']);
                    }

                    if ($recievedData['pictures'] != $event->getPictures()) {
                        $event->setPictures($recievedData['pictures']);
                    }

                    if ($recievedData['start_date'] != $event->getStartDate()->getTimestamp()) {
                        $event->setStartDate($myFunctions->timestampToDatetime($recievedData['start_date']));
                    }

                    if ($recievedData['end_date'] != $event->getEndDate()->getTimestamp()) {
                        $event->setEndDate($myFunctions->timestampToDatetime($recievedData['end_date']));
                    }

                    $violations = $validator->validate($event);

                    if (count($violations) > 0) {
                        $response = new JsonResponse();
                        $response->setContent($serializer->serialize($violations, 'json'));
                        return $response;
                    } else {
                        $manager->persist($event);
                        $manager->flush();
                        $response = new JsonResponse();
                        $response->setContent($serializer->serialize($event, 'json', ['groups' => 'event']));
                        return $response;
                    }
                } else {
                    return new Response("Seul l'auteur est autorisé à modifier son évènement.", Response::HTTP_UNAUTHORIZED);
                }
            } else {
                return new Response("L'évènement demandé n'a pas été trouvé en base de données.", Response::HTTP_NOT_FOUND);
            }

        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($errors, 'json'));
            return $response;
        }
    }
}

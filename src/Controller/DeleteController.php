<?php

namespace App\Controller;

use App\Utils\MyFunctions;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @Route("/delete", methods={"DELETE"}) */
class DeleteController extends AbstractController {

    /** @Route("/user/{slug}") */
    public function deleteUser(Request $request, String $slug, UserRepository $userRepository, ObjectManager $manager, SerializerInterface $serializer) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['userId'], $recievedData);

        if (count($errors) == 0) {
            if ( $user = $userRepository->findOneBySlug($slug)) {
                if ($recievedData['userId'] == $user->getId()) {
                    $manager->remove($user);
                    $manager->flush();
                    return new Response("L'utilisateur a correctement été supprimé de la base de données.", Response::HTTP_FOUND);
                } else {
                    return new Response("L'id fourni ne correspond pas avec l'utilisateur ayant ce slug.", Response::HTTP_UNAUTHORIZED);
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
    public function deleteAd(Request $request, String $slug, UserRepository $userRepository, AdRepository $adRepository, ObjectManager $manager, SerializerInterface $serializer) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['adId', 'author'], $recievedData);

        if (count($errors) == 0) {
            if ($ad = $adRepository->findOneBySlug($slug)) {
                if ($recievedData['adId'] == $ad->getId()) {
                    if ($recievedData['author'] == $ad->getAuthor()->getId()) {
                        $manager->remove($ad);
                        $manager->flush();
                        return new Response("L'annonce a correctement été supprimée de la base de données.", Response::HTTP_FOUND);
                    } else {
                        return new Response("Seul l'auteur est autorisé à supprimer son annonce.", Response::HTTP_UNAUTHORIZED);
                    }
                } else {
                    return new Response("L'id fourni ne correspond pas avec l'annonce ayant ce slug.", Response::HTTP_UNAUTHORIZED);
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
    public function deleteEvent(Request $request, String $slug, UserRepository $userRepository, EventRepository $eventRepository, ObjectManager $manager, SerializerInterface $serializer) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['eventId', 'author'], $recievedData);

        if (count($errors) == 0) {
            if ($event = $eventRepository->findOneBySlug($slug)) {
                if ($recievedData['eventId'] == $event->getId()) {
                    if ($recievedData['author'] == $event->getAuthor()->getId()) {
                        $manager->remove($event);
                        $manager->flush();
                        return new Response("L'évènement a correctement été supprimé de la base de données.", Response::HTTP_FOUND);
                    } else {
                        return new Response("Seul l'auteur est autorisé à supprimer son évènement.", Response::HTTP_UNAUTHORIZED);
                    }
                } else {
                    return new Response("L'id fourni ne correspond pas avec l'évènement ayant ce slug.", Response::HTTP_UNAUTHORIZED);
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

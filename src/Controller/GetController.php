<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @Route("/get", methods={"GET"}) */
class GetController extends AbstractController {
    
    /** @Route("/user/findAll") */
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer) {
        if ($users = $userRepository->findAll()) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($users, 'json', ['groups' => 'user']));
            return $response;
        } else {
            $contentToReturn = $myFunctions->returnErrorMessage("Aucun utilisateur présent en base de données.");
            $response = new JsonResponse();
            $response
                ->setContent($contentToReturn)
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }
    
    /** @Route("/user/{slug}") */
    public function getOneUser(String $slug, UserRepository $userRepository, SerializerInterface $serializer) {
        if ($user = $userRepository->findOneBySlug($slug)) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($user, 'json', ['groups' => 'user']));
            return $response;
        } else {
            $contentToReturn = $myFunctions->returnErrorMessage("L'utilisateur demandé n'as pas été trouvé en base de données.");
            $response = new JsonResponse();
            $response
                ->setContent($contentToReturn)
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }

    /** @Route("/ad/findAll") */
    public function getAllAds(AdRepository $adRepository, SerializerInterface $serializer) {
        if ($ads = $adRepository->findAll()) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($ads, 'json', ['groups' => 'ad']));
            return $response;
        } else {
            $contentToReturn = $myFunctions->returnErrorMessage("Aucune annonce présente en base de données.");
            $response = new JsonResponse();
            $response
                ->setContent($contentToReturn)
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }

    /** @Route("/ad/{slug}") */
    public function getOneAd(String $slug, AdRepository $adRepository, SerializerInterface $serializer) {
        if ($ad = $adRepository->findOneBySlug($slug)) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($ad, 'json', ['groups' => 'ad']));
            return $response;
        } else {
            $contentToReturn = $myFunctions->returnErrorMessage("L'annonce demandée n'as pas été trouvée en base de données.");
            $response = new JsonResponse();
            $response
                ->setContent($contentToReturn)
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }

    /** @Route("/event/findAll") */
    public function getAllEvents(EventRepository $eventRepository, SerializerInterface $serializer) {
        if ($events = $eventRepository->findAll()) {
            foreach ($events as $event) {
                $event
                    ->setStartDate($event->getStartDate()->getTimestamp())
                    ->setEndDate($event->getEndDate()->getTimestamp())
                ;
            }
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($events, 'json', ['groups' => 'event']));
            return $response;
        } else {
            $contentToReturn = $myFunctions->returnErrorMessage("Aucun évènement présent en base de données.");
            $response = new JsonResponse();
            $response
                ->setContent($contentToReturn)
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }

    /** @Route("/event/{slug}") */
    public function getOneEvent(String $slug, EventRepository $eventRepository, SerializerInterface $serializer) {
        if ($event = $eventRepository->findOneBySlug($slug)) {
            $event
                ->setStartDate($event->getStartDate()->getTimestamp())
                ->setEndDate($event->getEndDate()->getTimestamp())
            ;
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($event, 'json', ['groups' => 'event']));
            return $response;
        } else {
            $contentToReturn = $myFunctions->returnErrorMessage("L'évènement demandé n'as pas été trouvé en base de données.");
            $response = new JsonResponse();
            $response
                ->setContent($contentToReturn)
                ->setStatusCode(400, "Une erreur est survenue.")
            ;
            return $response;
        }
    }
}
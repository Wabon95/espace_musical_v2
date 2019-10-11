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
            return new Response("Aucun utilisateur présent en base de données.", Response::HTTP_NOT_FOUND);
        }
    }
    
    /** @Route("/user/{slug}") */
    public function getOneUser(String $slug, UserRepository $userRepository, SerializerInterface $serializer) {
        if ($user = $userRepository->findOneBySlug($slug)) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($user, 'json', ['groups' => 'user']));
            return $response;
        } else {
            return new Response("L'utilisateur demandé n'a pas été trouvé en base de données.", Response::HTTP_NOT_FOUND);
        }
    }

    /** @Route("/ad/findAll") */
    public function getAllAds(AdRepository $adRepository, SerializerInterface $serializer) {
        if ($ads = $adRepository->findAll()) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($ads, 'json', ['groups' => 'ad']));
            return $response;
        } else {
            return new Response("Aucune annonce présente en base de données.", Response::HTTP_NOT_FOUND);
        }
    }

    /** @Route("/ad/{slug}") */
    public function getOneAd(String $slug, AdRepository $adRepository, SerializerInterface $serializer) {
        if ($ad = $adRepository->findOneBySlug($slug)) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($ad, 'json', ['groups' => 'ad']));
            return $response;
        } else {
            return new Response("L'annonce demandée n'a pas été trouvée en base de données.", Response::HTTP_NOT_FOUND);
        }
    }

    /** @Route("/event/findAll") */
    public function getAllEvents(EventRepository $eventRepository, SerializerInterface $serializer) {
        if ($events = $eventRepository->findAll()) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($events, 'json', ['groups' => 'event']));
            return $response;
        } else {
            return new Response("Aucun évènements présent en base de données.", Response::HTTP_NOT_FOUND);
        }
    }

    /** @Route("/event/{slug}") */
    public function getOneEvent(String $slug, EventRepository $eventRepository, SerializerInterface $serializer) {
        if ($event = $eventRepository->findOneBySlug($slug)) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($event, 'json', ['groups' => 'event']));
            return $response;
        } else {
            return new Response("L'event demandé n'a pas été trouvé en base de données.", Response::HTTP_NOT_FOUND);
        }
    }
}
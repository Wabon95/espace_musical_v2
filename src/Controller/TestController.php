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

/** @Route("/get2", methods={"GET"}) */
class TestController extends AbstractController {

    /** @Route("/user/findAll") */
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer) {
        if ($users = $userRepository->findAll()) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($users, 'json', ['groups' => 'user']));
            return $response;
            // return $this->render('base.html.twig');
        } else {
            return new Response("Aucun utilisateur présent en base de données.", Response::HTTP_NOT_FOUND);
        }
    }
}

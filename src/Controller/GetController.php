<?php

namespace App\Controller;

use App\Repository\UserRepository;
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
            return new Response("Aucun utilisateur présent en base de données.");
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
}
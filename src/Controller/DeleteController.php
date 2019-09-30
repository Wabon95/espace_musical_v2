<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @Route("/delete", methods={"DELETE"}) */
class DeleteController extends AbstractController {

    /** @Route("/user/{slug}") */
    public function deleteUser(String $slug, UserRepository $userRepository, ObjectManager $manager) {
        if ($user = $userRepository->findOneBySlug($slug)) {
            $manager->remove($user);
            $manager->flush();
            return new Response("L'utilisateur a correctement été supprimé de la base de données.", Response::HTTP_FOUND);
        } else {
            return new Response("L'utilisateur demandé n'a pas été trouvé en base de données.", Response::HTTP_NOT_FOUND);
        }
    }
}

<?php

namespace App\Controller;

use App\Utils\MyFunctions;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @Route("/delete", methods={"DELETE"}) */
class DeleteController extends AbstractController {

    /** @Route("/user/{slug}") */
    public function deleteUser(Request $request, String $slug, UserRepository $userRepository, ObjectManager $manager) {
        $recievedData = json_decode($request->getContent(), true);

        $myFunctions = new MyFunctions();
        $errors = $myFunctions->multiple_array_key_exist(['userId'], $recievedData);

        if (!$errors) {
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
        } else {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($errors, 'json'));
            return $response;
        }
    }

    /** @Route("ad/{slug}") */
    public function deleteAd() {
        // TODO: Vérifier la présence de toutes les propriétés Json
        // TODO: Vérifier la présence d'un ad avec ce slug en bdd.
        // TODO: Vérifier que l'ad trouvée possède bien le même author.id que celui passé en paramètre de la requête.
        // TODO: Supprimer l'ad
    }
}

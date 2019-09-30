<?php

namespace App\Controller;

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

/** @Route("/put", methods={"PUT"}) */
class PutController extends AbstractController {

    /** @Route("/user/{slug}") */
    public function editUser(String $slug, Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, SerializerInterface $serializer, ObjectManager $manager, UserRepository $userRepository) {
        $recievedData = json_decode($request->getContent(), true);
        $errors = [];

        // On vérifie que toutes les propriétées sont bien renseignées
        if (!array_key_exists('email', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'email'.";
        if (!array_key_exists('username', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'username'.";
        if (!array_key_exists('firstname', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'firstname'.";
        if (!array_key_exists('lastname', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'lastname'.";
        if (!array_key_exists('address', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'address'.";
        if (!array_key_exists('currentPassword', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'currentPassword'.";
        if (!array_key_exists('newPassword', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'newPassword'.";
        if (!array_key_exists('newPasswordConfirm', $recievedData)) $errors[] = "Vous n'avez pas renseigné de proprietée 'newPasswordConfirm'.";

        if (count($errors) == 0) {
            if ($user = $userRepository->findOneBySlug($slug)) {
                if (password_verify($recievedData['currentPassword'], $user->getPassword())) {
                    $passwordHasModified = false;

                    if ($recievedData['email'] != '') {
                        if ($recievedData['email'] != $user->getEmail()) {
                            $user->setEmail($recievedData['email']);
                        } else {
                            return new Response("La nouvelle adresse email est identique à l'ancienne.", Response::HTTP_PRECONDITION_FAILED);
                        }
                    }

                    if ($recievedData['username'] != '') {
                        if ($recievedData['username'] != $user->getUsername()) {
                            $slugify = new Slugify();
                            $user
                                ->setUsername($recievedData['username'])
                                ->setSlug($slugify->slugify($recievedData['username']))    
                            ;
                        } else {
                            return new Response("Le nouveau pseudo est identique à l'ancien.", Response::HTTP_PRECONDITION_FAILED);
                        }
                    }

                    if ($recievedData['firstname'] != '') {
                        if ($recievedData['firstname'] != $user->getFirstname()) {
                            $user->setFirstname($recievedData['firstname']);
                        } else {
                            return new Response("Le nouveau prénom est identique à l'ancien.", Response::HTTP_PRECONDITION_FAILED);
                        }
                    }

                    if ($recievedData['lastname'] != '') {
                        if ($recievedData['lastname'] != $user->getLastname()) {
                            $user->setLastname($recievedData['lastname']);
                        } else {
                            return new Response("Le nouveau nom est identique à l'ancien.", Response::HTTP_PRECONDITION_FAILED);
                        }
                    }

                    if ($recievedData['address'] != '') {
                        if ($recievedData['address'] != $user->getAddress()) {
                            $user->setAddress($recievedData['address']);
                        } else {
                            return new Response("La nouvelle adresse est identique à l'ancienne.", Response::HTTP_PRECONDITION_FAILED);
                        }
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

                    // On vérifie les contraintes de validation
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
                return new Response("L'utilisateur demandé n'a pas été trouvé en base de données.", Response::HTTP_NOT_FOUND);
            }
        } elseif (count($errors) > 0) {
            $response = new JsonResponse();
            $response->setContent($serializer->serialize($errors, 'json'));
            return $response;
        }
    }
}

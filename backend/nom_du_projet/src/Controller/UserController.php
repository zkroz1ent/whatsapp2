<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/users", name="list_users", methods={"GET"})
     */
    public function listUsers(): JsonResponse
    {
        try {
            $users = $this->entityManager->getRepository(User::class)->findAll();
            $userData = [];

            foreach ($users as $user) {
                $userData[] = [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail()  // Adjust fields as necessary
                ];
            }

            return new JsonResponse($userData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving users'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
<?php

namespace App\Controller;

use App\Entity\Test1;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'registration', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation des données reçues
            if (!isset($data['username']) || !isset($data['password'])) {
                return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Création de l'utilisateur
            $user = new Test1();
            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['password']
                )
            );

            // Sauvegarde de l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'User successfully registered'], JsonResponse::HTTP_CREATED);
        } catch (UnexpectedValueException $e) {
            return new JsonResponse(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Internal server error: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
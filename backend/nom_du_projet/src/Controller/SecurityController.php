<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\JWTService;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    private $jwtService;
    private $logger;

    public function __construct(JWTService $jwtService, LoggerInterface $logger)
    {
        $this->jwtService = $jwtService;
        $this->logger = $logger;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Journalisation du nom d'utilisateur reçu
        $this->logger->info('Received username: ' . $data['username']);

        // Récupération de l'utilisateur par son nom d'utilisateur
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $data['username']]);
        if (!$user) {
            // Journalisation de l'utilisateur non trouvé
            $this->logger->error('User not found: ' . $data['username']);
            return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Vérification de la validité du mot de passe
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            // Journalisation du mot de passe invalide
            $this->logger->error('Invalid password for user: ' . $data['username']);
            return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Journalisation de l'authentification réussie
        $this->logger->info('User authenticated: ' . $data['username']);

        // Création du JWT
        $jwt = $this->jwtService->createToken($user);

        // Journalisation de la création du JWT
        $this->logger->info('JWT created for user: ' . $data['username']);

        // Retourner le JWT et l'ID de l'utilisateur
        return new JsonResponse([
            'token' => $jwt,
            'userId' => $user->getId()
        ], JsonResponse::HTTP_OK);
    }
    #[Route('/api/user-info/{id}', name: 'api_user_info', methods: ['GET'])]
    public function getUserInfo(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {

            $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

            if (!$user) {
                $this->logger->error('No user found with ID: ' . $id);
                return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $userInfo = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                // 'name' => $user->getName(),
                'email' => $user->getEmail(),
            ];

            $this->logger->info('User info retrieved for user: ' . $user->getUsername());

            return new JsonResponse($userInfo, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving user info: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred while retrieving user info'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/update-user/{id}', name: 'update-user', methods: ['PATCH'])]
  
    public function updateUserInfo(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

            if (!$user) {
                $this->logger->error('No user found with ID: ' . $id);
                return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['username'])) {
                $user->setUsername($data['username']);
            }

            if (isset($data['email'])) {
                $user->setEmail($data['email']);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->logger->info('User info updated for user: ' . $user->getUsername());

            return new JsonResponse(['message' => 'User info updated successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error updating user info: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred while updating user info'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

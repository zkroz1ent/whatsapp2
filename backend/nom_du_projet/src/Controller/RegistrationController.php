<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/register', name: 'registration', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Lecture et décodage des données de la requête
            $data = json_decode($request->getContent(), true);

            // Validation des données reçues
            if (!isset($data['username']) || !isset($data['password']) || !isset($data['email'])) {
                return new JsonResponse(['message' => 'Invalid input. Username, password, and email are required.'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Création de l'utilisateur
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['password']
                )
            );
            $user->setEmail($data['email']);
            $user->setPhonenumber($data['phonenumber']);
            // Journaliser l'objet User avant persistance
            $this->logger->info('User object before persistence:', [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                // Ne pas loguer le mot de passe pour des raisons de sécurité
            ]);

            // Sauvegarde de l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'User successfully registered'], JsonResponse::HTTP_CREATED);

        } catch (UnexpectedValueException $e) {
            $this->logger->warning('Unexpected value received.', ['exception' => $e]);
            return new JsonResponse(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Loguer l'erreur dans le logger
            $this->logger->error('User registration failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return new JsonResponse(['message' => 'Internal server error. Please try again later.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
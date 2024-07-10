<?php
namespace App\Controller;

use App\Entity\Test1;
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

        // $this->logger->info('Received username: ' . $data['username']);

        $user = $entityManager->getRepository(Test1::class)->findOneBy(['username' => $data['username']]);
        if (!$user) {
            // $this->logger->error('User not found: ' . $data['username']);
            return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            // $this->logger->error('Invalid password for user: ' . $data['username']);
            return new JsonResponse(['message' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // $this->logger->info('User authenticated: ' . $data['username']);

        // Correct method call
        $jwt = $this->jwtService->createToken($user);

        // $this->logger->info('JWT created for user: ' . $data['username']);

        return new JsonResponse(['token' => $jwt], JsonResponse::HTTP_OK);
    }
}
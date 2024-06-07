<?php

namespace App\Controller;

use App\Entity\Test1;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\JWTService;

class SecurityController extends AbstractController
{
    private $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $entityManager->getRepository(Test1::class)->findOneBy(['username' => $data['username']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid credentials.');
        }

        $token = $this->jwtService->createToken(['username' => $user->getUsername()]);
        return $this->json(['token' => $token]);
    }
}
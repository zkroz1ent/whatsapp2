<?php

namespace App\Service;

use App\Entity\Test1;
use Firebase\JWT\JWT;
use Psr\Log\LoggerInterface;

class JWTService
{
    private $secretKey;
    private $logger;

    public function __construct(string $secretKey, LoggerInterface $logger)
    {
        $this->secretKey = $secretKey;
        $this->logger = $logger;
    }

    public function createToken(Test1 $user): string
    {
        $payload = [
            'user' => $user->getUsername(),
            'exp' => (new \DateTime('+1 hour'))->getTimestamp()
        ];

        $this->logger->info('Creating JWT token for user: ' . $user->getUsername());

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
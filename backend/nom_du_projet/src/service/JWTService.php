<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function createToken(array $data): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;  // token valide pour 1 heure
        $payload = array_merge($data, [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
        ]);

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): array
    {
        return (array) JWT::decode($token, new Key($this->secretKey, 'HS256'));
    }
}
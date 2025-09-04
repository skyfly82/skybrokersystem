<?php

namespace App\Security;

use App\Entity\CustomerUser;
use App\Entity\SystemUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTManager
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager
    ) {
    }

    public function createToken(UserInterface $user): string
    {
        $payload = $this->buildTokenPayload($user);
        return $this->jwtManager->createFromPayload($user, $payload);
    }

    public function parseToken(string $token): ?array
    {
        try {
            return $this->jwtManager->parse($token);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function buildTokenPayload(UserInterface $user): array
    {
        $payload = [
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
        ];

        if ($user instanceof CustomerUser) {
            $payload['user_type'] = 'customer';
            $payload['customer_id'] = $user->getCustomer()?->getId();
            $payload['company_role'] = $user->getCustomerRole();
            $payload['status'] = $user->getStatus();
        } elseif ($user instanceof SystemUser) {
            $payload['user_type'] = 'system';
            $payload['department'] = $user->getDepartment();
            $payload['position'] = $user->getPosition();
            $payload['status'] = $user->getStatus();
        }

        return $payload;
    }

    public function refreshToken(string $token): ?string
    {
        $payload = $this->parseToken($token);
        if (!$payload) {
            return null;
        }

        // Check if token is close to expiry (within 15 minutes)
        $currentTime = time();
        $expiryTime = $payload['exp'] ?? 0;
        
        if ($expiryTime - $currentTime < 900) {
            // Token is close to expiry, create new one
            $payload['iat'] = $currentTime;
            $payload['exp'] = $currentTime + 3600;
            
            return $this->jwtManager->createFromPayload(null, $payload);
        }

        return $token; // Token still valid for more than 15 minutes
    }

    public function validateTokenStructure(array $payload): bool
    {
        $requiredFields = ['email', 'roles', 'user_type', 'iat', 'exp'];
        
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field])) {
                return false;
            }
        }

        // Validate user type specific fields
        if ($payload['user_type'] === 'customer') {
            return isset($payload['company_role'], $payload['status']);
        } elseif ($payload['user_type'] === 'system') {
            return isset($payload['department'], $payload['status']);
        }

        return false;
    }
}
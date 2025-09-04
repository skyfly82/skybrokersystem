<?php

namespace App\Security;

use App\Entity\SystemUser;
use App\Security\JWTManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Psr\Log\LoggerInterface;

class SystemAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private JWTManager $jwtManager,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        /** @var SystemUser $user */
        $user = $token->getUser();
        
        try {
            // Update last login time
            $user->setLastLoginAt(new \DateTime());
            $this->entityManager->flush();
            
            // Generate JWT token with enhanced security
            $jwt = $this->jwtManager->createToken($user);
            
            $this->logger->info('System user login successful', [
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'ip' => $request->getClientIp()
            ]);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'token' => $jwt,
                'tokenType' => 'Bearer',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'fullName' => $user->getFullName(),
                    'department' => $user->getDepartment(),
                    'position' => $user->getPosition(),
                    'role' => $user->getRole(),
                    'status' => $user->getStatus(),
                    'roles' => $user->getRoles()
                ],
                'expiresAt' => (new \DateTime())->modify('+1 hour')->format('c')
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('System user login error', [
                'user_id' => $user->getId(),
                'error' => $e->getMessage()
            ]);
            
            return new JsonResponse([
                'success' => false,
                'message' => 'Authentication error occurred'
            ], 500);
        }
    }
}
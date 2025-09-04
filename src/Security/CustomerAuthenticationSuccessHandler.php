<?php

namespace App\Security;

use App\Entity\CustomerUser;
use App\Security\JWTManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Psr\Log\LoggerInterface;

class CustomerAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private JWTManager $jwtManager,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        /** @var CustomerUser $user */
        $user = $token->getUser();
        
        try {
            // Update last login time
            $user->setLastLoginAt(new \DateTime());
            $this->entityManager->flush();
            
            // Generate JWT token with enhanced security
            $jwt = $this->jwtManager->createToken($user);
            
            // Prepare user data for response
            $userData = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'fullName' => $user->getFullName(),
                'companyRole' => $user->getCustomerRole(),
                'status' => $user->getStatus()
            ];
            
            // Add customer data if available
            if ($customer = $user->getCustomer()) {
                $userData['customer'] = [
                    'id' => $customer->getId(),
                    'companyName' => $customer->getCompanyName(),
                    'type' => $customer->getType(),
                    'status' => $customer->getStatus()
                ];
            }
            
            $this->logger->info('Customer user login successful', [
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
                'ip' => $request->getClientIp()
            ]);
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'token' => $jwt,
                'tokenType' => 'Bearer',
                'user' => $userData,
                'expiresAt' => (new \DateTime())->modify('+1 hour')->format('c')
            ]);
            
        } catch (\Exception $e) {
            $this->logger->error('Customer login error', [
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
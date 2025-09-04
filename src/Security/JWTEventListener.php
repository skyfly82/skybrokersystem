<?php

namespace App\Security;

use App\Entity\CustomerUser;
use App\Entity\SystemUser;
use App\Repository\CustomerUserRepository;
use App\Repository\SystemUserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;

class JWTEventListener
{
    public function __construct(
        private RequestStack $requestStack,
        private CustomerUserRepository $customerUserRepository,
        private SystemUserRepository $systemUserRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * Add custom data to JWT payload when token is created
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();
        $request = $this->requestStack->getCurrentRequest();

        // Add IP address and user agent for security tracking
        if ($request) {
            $payload['ip'] = $request->getClientIp();
            $payload['user_agent'] = $request->headers->get('User-Agent');
        }

        // Add user-specific data
        if ($user instanceof CustomerUser) {
            $payload['user_type'] = 'customer';
            $payload['customer_id'] = $user->getCustomer()?->getId();
            $payload['company_role'] = $user->getCustomerRole();
            $payload['status'] = $user->getStatus();
            
            if ($user->getCustomer()) {
                $payload['company_name'] = $user->getCustomer()->getCompanyName();
                $payload['customer_type'] = $user->getCustomer()->getType();
            }
        } elseif ($user instanceof SystemUser) {
            $payload['user_type'] = 'system';
            $payload['department'] = $user->getDepartment();
            $payload['position'] = $user->getPosition();
            $payload['status'] = $user->getStatus();
        }

        // Add timestamp
        $payload['created_at'] = time();
        
        $event->setData($payload);

        $this->logger->info('JWT token created', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'user_type' => $payload['user_type'] ?? 'unknown'
        ]);
    }

    /**
     * Validate JWT payload after decoding
     */
    public function onJWTDecoded(JWTDecodedEvent $event): void
    {
        $payload = $event->getPayload();
        $request = $this->requestStack->getCurrentRequest();

        // Validate required fields
        if (!isset($payload['email'], $payload['user_type'])) {
            $event->markAsInvalid();
            $this->logger->warning('JWT token missing required fields', $payload);
            return;
        }

        // Validate IP address (optional security measure)
        if (isset($payload['ip']) && $request) {
            $currentIp = $request->getClientIp();
            if ($payload['ip'] !== $currentIp) {
                $this->logger->warning('JWT token IP mismatch', [
                    'token_ip' => $payload['ip'],
                    'current_ip' => $currentIp,
                    'email' => $payload['email']
                ]);
                // Note: We're not invalidating the token here for IP mismatch
                // as users might have dynamic IPs, but we're logging it for monitoring
            }
        }

        // Validate user still exists and is active
        try {
            if ($payload['user_type'] === 'customer') {
                $user = $this->customerUserRepository->findOneBy(['email' => $payload['email']]);
                if (!$user || $user->getStatus() !== 'active') {
                    $event->markAsInvalid();
                    $this->logger->warning('JWT token for inactive/non-existent customer user', [
                        'email' => $payload['email']
                    ]);
                    return;
                }
            } elseif ($payload['user_type'] === 'system') {
                $user = $this->systemUserRepository->findOneBy(['email' => $payload['email']]);
                if (!$user || $user->getStatus() !== 'active') {
                    $event->markAsInvalid();
                    $this->logger->warning('JWT token for inactive/non-existent system user', [
                        'email' => $payload['email']
                    ]);
                    return;
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Error validating JWT user existence', [
                'email' => $payload['email'],
                'error' => $e->getMessage()
            ]);
            $event->markAsInvalid();
        }
    }

    /**
     * Handle successful JWT authentication
     */
    public function onJWTAuthenticated(JWTAuthenticatedEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $payload = $event->getPayload();

        $this->logger->info('JWT authentication successful', [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'user_type' => $payload['user_type'] ?? 'unknown'
        ]);

        // Update last login time (optional)
        if ($user instanceof CustomerUser || $user instanceof SystemUser) {
            $user->setLastLoginAt(new \DateTime());
            // Note: You might want to flush this to database depending on your needs
        }
    }
}
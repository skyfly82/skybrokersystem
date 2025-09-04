<?php

namespace App\Controller;

use App\Entity\CustomerUser;
use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/invitations')]
class PublicInvitationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/{token}/info', name: 'api_public_invitation_info', methods: ['GET'])]
    public function getInvitationInfo(string $token): JsonResponse
    {
        $invitation = $this->entityManager->getRepository(Invitation::class)
            ->findOneBy(['token' => $token]);

        if (!$invitation) {
            return $this->json(['error' => 'Invalid invitation token'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'invitation' => [
                'email' => $invitation->getEmail(),
                'fullName' => $invitation->getFullName(),
                'customerRole' => $invitation->getCustomerRole(),
                'status' => $invitation->getStatus(),
                'createdAt' => $invitation->getCreatedAt()->format('c'),
                'expiresAt' => $invitation->getExpiresAt()->format('c'),
                'isExpired' => $invitation->isExpired(),
                'canBeAccepted' => $invitation->canBeAccepted()
            ],
            'customer' => [
                'companyName' => $invitation->getCustomer()->getCompanyName(),
                'type' => $invitation->getCustomer()->getType()
            ],
            'invitedBy' => [
                'fullName' => $invitation->getInvitedBy()->getFullName()
            ]
        ]);
    }

    #[Route('/{token}/accept', name: 'api_public_accept_invitation', methods: ['POST'])]
    public function acceptInvitation(string $token, Request $request): JsonResponse
    {
        $invitation = $this->entityManager->getRepository(Invitation::class)
            ->findOneBy(['token' => $token]);

        if (!$invitation) {
            return $this->json(['error' => 'Invalid invitation token'], Response::HTTP_NOT_FOUND);
        }

        if (!$invitation->canBeAccepted()) {
            $message = $invitation->isExpired() ? 'Invitation has expired' : 'Invitation is no longer valid';
            return $this->json(['error' => $message], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data || empty($data['password'])) {
            return $this->json(['error' => 'Password is required'], Response::HTTP_BAD_REQUEST);
        }

        // Check if email already exists as a user (double-check)
        $existingUser = $this->entityManager->getRepository(CustomerUser::class)
            ->findOneBy(['email' => $invitation->getEmail()]);
        
        if ($existingUser) {
            return $this->json(['error' => 'User with this email already exists'], Response::HTTP_CONFLICT);
        }

        try {
            // Create new customer user
            $customerUser = new CustomerUser();
            $customerUser->setEmail($invitation->getEmail())
                        ->setFirstName($invitation->getFirstName())
                        ->setLastName($invitation->getLastName())
                        ->setCustomerRole($invitation->getCustomerRole())
                        ->setStatus('active')
                        ->setCustomer($invitation->getCustomer());

            // Hash password
            $hashedPassword = $this->passwordHasher->hashPassword($customerUser, $data['password']);
            $customerUser->setPassword($hashedPassword);

            if (!empty($data['phone'])) {
                $customerUser->setPhone($data['phone']);
            }

            // Mark invitation as accepted
            $invitation->setStatus('accepted');
            $invitation->setAcceptedAt(new \DateTime());
            $invitation->setAcceptedBy($customerUser);

            // Save both entities
            $this->entityManager->persist($customerUser);
            $this->entityManager->flush();

            return $this->json([
                'message' => 'Invitation accepted successfully',
                'user' => [
                    'id' => $customerUser->getId(),
                    'email' => $customerUser->getEmail(),
                    'fullName' => $customerUser->getFullName(),
                    'customerRole' => $customerUser->getCustomerRole()
                ],
                'customer' => [
                    'id' => $customerUser->getCustomer()->getId(),
                    'companyName' => $customerUser->getCustomer()->getCompanyName(),
                    'type' => $customerUser->getCustomer()->getType()
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to accept invitation',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/customer')]
class CustomerInvitationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('/invitations', name: 'api_customer_invitations_list', methods: ['GET'])]
    public function listInvitations(): JsonResponse
    {
        /** @var CustomerUser $user */
        $user = $this->getUser();
        
        if (!$user instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Only owners and managers can view invitations
        if (!$user->canManageUsers()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $invitations = $this->entityManager->getRepository(Invitation::class)
            ->findBy(['customer' => $user->getCustomer()], ['createdAt' => 'DESC']);

        $invitationData = [];
        foreach ($invitations as $invitation) {
            $invitationData[] = [
                'id' => $invitation->getId(),
                'email' => $invitation->getEmail(),
                'fullName' => $invitation->getFullName(),
                'customerRole' => $invitation->getCustomerRole(),
                'status' => $invitation->getStatus(),
                'createdAt' => $invitation->getCreatedAt()->format('c'),
                'expiresAt' => $invitation->getExpiresAt()->format('c'),
                'acceptedAt' => $invitation->getAcceptedAt()?->format('c'),
                'invitedBy' => [
                    'id' => $invitation->getInvitedBy()->getId(),
                    'fullName' => $invitation->getInvitedBy()->getFullName()
                ],
                'isExpired' => $invitation->isExpired(),
                'isPending' => $invitation->isPending(),
                'canBeAccepted' => $invitation->canBeAccepted()
            ];
        }

        return $this->json([
            'invitations' => $invitationData,
            'total' => count($invitationData)
        ]);
    }

    #[Route('/invitations/send', name: 'api_customer_send_invitation', methods: ['POST'])]
    public function sendInvitation(Request $request): JsonResponse
    {
        /** @var CustomerUser $user */
        $user = $this->getUser();
        
        if (!$user instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Only owners and managers can send invitations
        if (!$user->canManageUsers()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        // Validate required fields
        $requiredFields = ['email', 'firstName', 'lastName', 'customerRole'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Field '$field' is required"], Response::HTTP_BAD_REQUEST);
            }
        }

        // Validate role
        $allowedRoles = ['manager', 'employee', 'viewer'];
        if (!in_array($data['customerRole'], $allowedRoles)) {
            return $this->json(['error' => 'Invalid customer role'], Response::HTTP_BAD_REQUEST);
        }

        // Check if email already exists as a user
        $existingUser = $this->entityManager->getRepository(CustomerUser::class)
            ->findOneBy(['email' => $data['email']]);
        
        if ($existingUser) {
            return $this->json(['error' => 'User with this email already exists'], Response::HTTP_CONFLICT);
        }

        // Check if there's already a pending invitation for this email
        $existingInvitation = $this->entityManager->getRepository(Invitation::class)
            ->findOneBy([
                'email' => $data['email'], 
                'customer' => $user->getCustomer(),
                'status' => 'pending'
            ]);

        if ($existingInvitation && $existingInvitation->isPending()) {
            return $this->json(['error' => 'Pending invitation already exists for this email'], Response::HTTP_CONFLICT);
        }

        try {
            // Create invitation
            $invitation = new Invitation();
            $invitation->setEmail($data['email'])
                      ->setFirstName($data['firstName'])
                      ->setLastName($data['lastName'])
                      ->setCustomerRole($data['customerRole'])
                      ->setCustomer($user->getCustomer())
                      ->setInvitedBy($user);

            // Validate invitation
            $errors = $this->validator->validate($invitation);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }

            // Save invitation
            $this->entityManager->persist($invitation);
            $this->entityManager->flush();

            // TODO: Send email notification here
            
            return $this->json([
                'message' => 'Invitation sent successfully',
                'invitation' => [
                    'id' => $invitation->getId(),
                    'email' => $invitation->getEmail(),
                    'fullName' => $invitation->getFullName(),
                    'customerRole' => $invitation->getCustomerRole(),
                    'token' => $invitation->getToken(), // Include token for testing
                    'expiresAt' => $invitation->getExpiresAt()->format('c')
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Failed to send invitation',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/invitations/{id}/cancel', name: 'api_customer_cancel_invitation', methods: ['DELETE'])]
    public function cancelInvitation(int $id): JsonResponse
    {
        /** @var CustomerUser $user */
        $user = $this->getUser();
        
        if (!$user instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Only owners and managers can cancel invitations
        if (!$user->canManageUsers()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $invitation = $this->entityManager->getRepository(Invitation::class)->find($id);
        
        if (!$invitation) {
            return $this->json(['error' => 'Invitation not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if invitation belongs to user's company
        if ($invitation->getCustomer() !== $user->getCustomer()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        // Can only cancel pending invitations
        if (!$invitation->isPending()) {
            return $this->json(['error' => 'Can only cancel pending invitations'], Response::HTTP_BAD_REQUEST);
        }

        $invitation->setStatus('cancelled');
        $this->entityManager->flush();

        return $this->json(['message' => 'Invitation cancelled successfully']);
    }

    #[Route('/invitations/{token}/accept', name: 'api_accept_invitation', methods: ['POST'])]
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

    #[Route('/invitations/{token}/info', name: 'api_invitation_info', methods: ['GET'])]
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
}
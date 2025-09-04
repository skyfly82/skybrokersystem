<?php

namespace App\Controller;

use App\Entity\CustomerUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/customer')]
class CustomerAuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/login', name: 'api_customer_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // This method will be handled by the security system
        // The actual authentication is done by the json_login in security.yaml
        // This is just a placeholder that won't be executed
        return $this->json(['message' => 'Login endpoint - handled by security system']);
    }

    #[Route('/profile', name: 'api_customer_profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        /** @var CustomerUser $user */
        $user = $this->getUser();
        
        if (!$user instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'fullName' => $user->getFullName(),
                'phone' => $user->getPhone(),
                'customerRole' => $user->getCustomerRole(),
                'status' => $user->getStatus(),
                'emailVerified' => $user->isEmailVerified(),
                'createdAt' => $user->getCreatedAt()->format('c'),
                'lastLoginAt' => $user->getLastLoginAt()?->format('c')
            ],
            'customer' => [
                'id' => $user->getCustomer()->getId(),
                'companyName' => $user->getCustomer()->getCompanyName(),
                'type' => $user->getCustomer()->getType(),
                'email' => $user->getCustomer()->getEmail(),
                'phone' => $user->getCustomer()->getPhone(),
                'address' => $user->getCustomer()->getAddress(),
                'city' => $user->getCustomer()->getCity(),
                'country' => $user->getCustomer()->getCountry(),
                'vatNumber' => $user->getCustomer()->getVatNumber(),
                'regon' => $user->getCustomer()->getRegon(),
                'status' => $user->getCustomer()->getStatus()
            ],
            'permissions' => [
                'canManageUsers' => $user->canManageUsers(),
                'isOwner' => $user->isOwner(),
                'isManager' => $user->isManager()
            ]
        ]);
    }

    #[Route('/company-users', name: 'api_customer_company_users', methods: ['GET'])]
    public function getCompanyUsers(): JsonResponse
    {
        /** @var CustomerUser $user */
        $user = $this->getUser();
        
        if (!$user instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Only owners and managers can see other users
        if (!$user->canManageUsers()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $companyUsers = $this->entityManager->getRepository(CustomerUser::class)
            ->findBy(['customer' => $user->getCustomer()], ['createdAt' => 'ASC']);

        $users = [];
        foreach ($companyUsers as $companyUser) {
            $users[] = [
                'id' => $companyUser->getId(),
                'email' => $companyUser->getEmail(),
                'fullName' => $companyUser->getFullName(),
                'customerRole' => $companyUser->getCustomerRole(),
                'status' => $companyUser->getStatus(),
                'createdAt' => $companyUser->getCreatedAt()->format('c'),
                'lastLoginAt' => $companyUser->getLastLoginAt()?->format('c'),
                'emailVerified' => $companyUser->isEmailVerified()
            ];
        }

        return $this->json([
            'users' => $users,
            'total' => count($users)
        ]);
    }

    #[Route('/logout', name: 'api_customer_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // For stateless JWT, logout is handled on the client side
        // Server doesn't need to do anything special
        return $this->json(['message' => 'Logged out successfully']);
    }

    #[Route('/company-users/{id}', name: 'api_customer_get_user', methods: ['GET'])]
    public function getCompanyUser(int $id): JsonResponse
    {
        /** @var CustomerUser $currentUser */
        $currentUser = $this->getUser();
        
        if (!$currentUser instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$currentUser->canManageUsers()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $user = $this->entityManager->getRepository(CustomerUser::class)->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if user belongs to same company
        if ($user->getCustomer() !== $currentUser->getCustomer()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'fullName' => $user->getFullName(),
                'phone' => $user->getPhone(),
                'customerRole' => $user->getCustomerRole(),
                'status' => $user->getStatus(),
                'createdAt' => $user->getCreatedAt()->format('c'),
                'lastLoginAt' => $user->getLastLoginAt()?->format('c'),
                'emailVerified' => $user->isEmailVerified()
            ]
        ]);
    }

    #[Route('/company-users/{id}/role', name: 'api_customer_update_user_role', methods: ['PUT'])]
    public function updateUserRole(int $id, Request $request): JsonResponse
    {
        /** @var CustomerUser $currentUser */
        $currentUser = $this->getUser();
        
        if (!$currentUser instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Only owners can change roles
        if (!$currentUser->isOwner()) {
            return $this->json(['error' => 'Only owners can change user roles'], Response::HTTP_FORBIDDEN);
        }

        $user = $this->entityManager->getRepository(CustomerUser::class)->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if user belongs to same company
        if ($user->getCustomer() !== $currentUser->getCustomer()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        // Cannot change own role
        if ($user === $currentUser) {
            return $this->json(['error' => 'Cannot change your own role'], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data || empty($data['customerRole'])) {
            return $this->json(['error' => 'Customer role is required'], Response::HTTP_BAD_REQUEST);
        }

        $allowedRoles = ['owner', 'manager', 'employee', 'viewer'];
        if (!in_array($data['customerRole'], $allowedRoles)) {
            return $this->json(['error' => 'Invalid customer role'], Response::HTTP_BAD_REQUEST);
        }

        // Cannot create another owner
        if ($data['customerRole'] === 'owner') {
            return $this->json(['error' => 'Cannot assign owner role. Transfer ownership instead.'], Response::HTTP_BAD_REQUEST);
        }

        $user->setCustomerRole($data['customerRole']);
        $user->setUpdatedAt(new \DateTime());
        
        $this->entityManager->flush();

        return $this->json([
            'message' => 'User role updated successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'fullName' => $user->getFullName(),
                'customerRole' => $user->getCustomerRole()
            ]
        ]);
    }

    #[Route('/company-users/{id}/status', name: 'api_customer_update_user_status', methods: ['PUT'])]
    public function updateUserStatus(int $id, Request $request): JsonResponse
    {
        /** @var CustomerUser $currentUser */
        $currentUser = $this->getUser();
        
        if (!$currentUser instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$currentUser->canManageUsers()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $user = $this->entityManager->getRepository(CustomerUser::class)->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if user belongs to same company
        if ($user->getCustomer() !== $currentUser->getCustomer()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        // Cannot change own status
        if ($user === $currentUser) {
            return $this->json(['error' => 'Cannot change your own status'], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data || empty($data['status'])) {
            return $this->json(['error' => 'Status is required'], Response::HTTP_BAD_REQUEST);
        }

        $allowedStatuses = ['active', 'inactive'];
        if (!in_array($data['status'], $allowedStatuses)) {
            return $this->json(['error' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        }

        $user->setStatus($data['status']);
        $user->setUpdatedAt(new \DateTime());
        
        $this->entityManager->flush();

        return $this->json([
            'message' => 'User status updated successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'fullName' => $user->getFullName(),
                'status' => $user->getStatus()
            ]
        ]);
    }

    #[Route('/company-users/{id}', name: 'api_customer_remove_user', methods: ['DELETE'])]
    public function removeCompanyUser(int $id): JsonResponse
    {
        /** @var CustomerUser $currentUser */
        $currentUser = $this->getUser();
        
        if (!$currentUser instanceof CustomerUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Only owners can remove users
        if (!$currentUser->isOwner()) {
            return $this->json(['error' => 'Only owners can remove users'], Response::HTTP_FORBIDDEN);
        }

        $user = $this->entityManager->getRepository(CustomerUser::class)->find($id);
        
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if user belongs to same company
        if ($user->getCustomer() !== $currentUser->getCustomer()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        // Cannot remove yourself
        if ($user === $currentUser) {
            return $this->json(['error' => 'Cannot remove yourself'], Response::HTTP_BAD_REQUEST);
        }

        // Remove user
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User removed successfully']);
    }
}
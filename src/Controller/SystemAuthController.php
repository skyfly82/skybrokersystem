<?php

namespace App\Controller;

use App\Entity\SystemUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/system')]
class SystemAuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/login', name: 'api_system_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // This method will be handled by the security system
        // The actual authentication is done by the json_login in security.yaml
        // This is just a placeholder that won't be executed
        return $this->json(['message' => 'Login endpoint - handled by security system']);
    }

    #[Route('/profile', name: 'api_system_profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        /** @var SystemUser $user */
        $user = $this->getUser();
        
        if (!$user instanceof SystemUser) {
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
                'department' => $user->getDepartment(),
                'position' => $user->getPosition(),
                'status' => $user->getStatus(),
                'roles' => $user->getRoles(),
                'emailVerified' => $user->isEmailVerified(),
                'createdAt' => $user->getCreatedAt()->format('c'),
                'lastLoginAt' => $user->getLastLoginAt()?->format('c')
            ],
            'permissions' => [
                'isAdmin' => $user->isAdmin(),
                'isSupport' => $user->isSupport(),
                'isMarketing' => $user->isMarketing(),
                'isSales' => $user->isSales(),
                'isOperations' => $user->isOperations(),
                'canManageCustomers' => $user->canManageCustomers(),
                'canViewReports' => $user->canViewReports()
            ]
        ]);
    }

    #[Route('/team', name: 'api_system_team', methods: ['GET'])]
    public function getTeam(): JsonResponse
    {
        /** @var SystemUser $user */
        $user = $this->getUser();
        
        if (!$user instanceof SystemUser) {
            return $this->json(['error' => 'Not authenticated'], Response::HTTP_UNAUTHORIZED);
        }

        // Only admins can see all team members
        if (!$user->isAdmin()) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $teamMembers = $this->entityManager->getRepository(SystemUser::class)
            ->findBy([], ['department' => 'ASC', 'firstName' => 'ASC']);

        $users = [];
        foreach ($teamMembers as $member) {
            $users[] = [
                'id' => $member->getId(),
                'email' => $member->getEmail(),
                'fullName' => $member->getFullName(),
                'department' => $member->getDepartment(),
                'position' => $member->getPosition(),
                'status' => $member->getStatus(),
                'createdAt' => $member->getCreatedAt()->format('c'),
                'lastLoginAt' => $member->getLastLoginAt()?->format('c')
            ];
        }

        return $this->json([
            'team' => $users,
            'total' => count($users)
        ]);
    }

    #[Route('/logout', name: 'api_system_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // For stateless JWT, logout is handled on the client side
        // Server doesn't need to do anything special
        return $this->json(['message' => 'Logged out successfully']);
    }
}
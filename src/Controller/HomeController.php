<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'title' => 'SkyBrokerSystem v2 API',
            'description' => 'Modern courier brokerage platform built with Symfony 7.x',
            'version' => '2.0.0-dev',
            'status' => 'development',
            'timestamp' => new \DateTime(),
            'links' => [
                'health_check' => $this->generateUrl('app_health'),
                'profiler' => '/_profiler',
                'api_documentation' => '/api/docs'
            ],
            'endpoints' => [
                'authentication' => [
                    'customer_login' => 'POST /api/v1/customer/login',
                    'system_login' => 'POST /api/v1/system/login',
                    'customer_register' => 'POST /api/v1/registration/register'
                ],
                'customer_api' => [
                    'profile' => 'GET /api/v1/customer/profile',
                    'company_users' => 'GET /api/v1/customer/company-users',
                    'invitations' => 'GET /api/v1/customer/invitations',
                    'send_invitation' => 'POST /api/v1/customer/invitations/send'
                ],
                'system_api' => [
                    'profile' => 'GET /api/v1/system/profile',
                    'team' => 'GET /api/v1/system/team'
                ],
                'public_api' => [
                    'invitation_info' => 'GET /api/v1/invitations/{token}/info',
                    'accept_invitation' => 'POST /api/v1/invitations/{token}/accept'
                ]
            ],
            'features' => [
                'multi_guard_authentication' => 'Separate authentication for customers and system users',
                'jwt_security' => 'Secure JWT token-based authentication with 1-hour expiry',
                'invitation_system' => 'Company user invitation and management system',
                'multi_tenant' => 'Support for multiple customer companies with role-based access',
                'audit_logging' => 'Comprehensive security and activity logging'
            ],
            'architecture' => [
                'framework' => 'Symfony 7.x',
                'php_version' => phpversion(),
                'database' => 'MySQL 8.0',
                'cache' => 'Redis',
                'patterns' => ['Domain-Driven Design', 'CQRS', 'API-First', 'Clean Architecture', 'Hexagonal Architecture']
            ],
            'migration_status' => [
                'from' => 'Laravel-based SkyBrokerSystem v1',
                'current_phase' => 'Phase 1: Foundation & Authentication',
                'completed_features' => [
                    'Symfony 7.1 setup',
                    'Multi-guard authentication',
                    'JWT token management',
                    'User entities and repositories',
                    'Invitation system',
                    'Security event listeners'
                ],
                'next_phase' => 'Phase 2: Core Business Logic (Order Management, Courier Integration)'
            ],
            'development_info' => [
                'environment' => $this->getParameter('kernel.environment'),
                'debug_mode' => $this->getParameter('kernel.debug'),
                'timezone' => date_default_timezone_get(),
                'locale' => $this->getParameter('kernel.default_locale')
            ]
        ]);
    }

    #[Route('/web', name: 'app_home_web', methods: ['GET'])]
    public function web(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route('/auth', name: 'app_auth', methods: ['GET'])]
    public function auth(): Response
    {
        return $this->render('auth.html.twig');
    }

    #[Route('/login', name: 'app_login_web', methods: ['GET'])]
    public function loginWeb(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/login/customer', name: 'app_login_customer_web', methods: ['GET'])]
    public function loginCustomerWeb(): Response
    {
        return $this->render('login_customer.html.twig');
    }

    #[Route('/login/admin', name: 'app_login_admin_web', methods: ['GET'])]
    public function loginAdminWeb(): Response
    {
        return $this->render('login_admin.html.twig');
    }

    #[Route('/register', name: 'app_register_web', methods: ['GET'])]
    public function registerWeb(): Response
    {
        return $this->render('register.html.twig');
    }

    #[Route('/forgot', name: 'app_forgot_web', methods: ['GET'])]
    public function forgotWeb(): Response
    {
        return $this->render('forgot.html.twig');
    }
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('dashboard.html.twig');
    }
}

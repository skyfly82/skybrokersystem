<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\CustomerUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\PreliminaryRegistration;
use App\Service\NipValidator;

#[Route('/api/v1/registration')]
class CustomerRegistrationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private NipValidator $nipValidator
    ) {}

    #[Route('/start', name: 'api_customer_register_step1', methods: ['POST'])]
    public function start(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['success' => false, 'message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $customerType = $data['customerType'] ?? null; // 'individual' | 'business'
        $country = strtoupper($data['country'] ?? 'PL');
        $nip = $data['nip'] ?? null;
        $unregistered = (bool)($data['unregisteredBusiness'] ?? false);
        $ssoProvider = $data['ssoProvider'] ?? null; // 'google'|'facebook'|'apple' or null

        if (!in_array($customerType, ['individual','business'], true)) {
            return $this->json(['success' => false, 'message' => 'Missing required fields: customerType'], Response::HTTP_BAD_REQUEST);
        }

        // For non-SSO flows, email and password are required at step 1
        if ($ssoProvider === null) {
            if (!$email || !$password) {
                return $this->json(['success' => false, 'message' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            // With SSO, require NIP for business (handled below) and allow missing email/password
        }

        // Email duplication check against existing users
        if ($email) {
            $existingUser = $this->entityManager->getRepository(\App\Entity\CustomerUser::class)
                ->findOneBy(['email' => $email]);
            if ($existingUser) {
                return $this->json(['success' => false, 'message' => 'Email already registered'], Response::HTTP_CONFLICT);
            }
        }

        // If business and not unregistered, validate NIP for PL (basic checksum for now)
        if ($customerType === 'business' && !$unregistered && $nip) {
            if ($country === 'PL') {
                if (!$this->nipValidator->isValidPlNip($nip)) {
                    return $this->json(['success' => false, 'message' => 'Invalid NIP number'], Response::HTTP_BAD_REQUEST);
                }
                // TODO: Integrate with BIR GUS to verify active status
            }
        }

        $pre = new PreliminaryRegistration();
        $pre->setEmail($email)
            ->setPasswordHash($password ? password_hash($password, PASSWORD_BCRYPT) : password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT))
            ->setCustomerType($customerType)
            ->setCountry($country)
            ->setUnregisteredBusiness($unregistered)
            ->setB2b(($customerType === 'business') || !empty($nip))
            ->setSsoProvider($ssoProvider);
        if ($nip) { $pre->setNip($nip); }

        $this->entityManager->persist($pre);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'Step 1 completed',
            'token' => $pre->getToken(),
            'b2b' => $pre->isB2b(),
            'next' => '/api/v1/registration/register'
        ], Response::HTTP_CREATED);
    }

    #[Route('/register', name: 'api_customer_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        // Validate required fields (companyName only for business)
        $requiredFields = ['email', 'firstName', 'lastName', 'customerType'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->json(['error' => "Field '$field' is required"], Response::HTTP_BAD_REQUEST);
            }
        }

        if (($data['customerType'] ?? null) === 'business' && empty($data['companyName'])) {
            return $this->json(['error' => "Field 'companyName' is required for business"], Response::HTTP_BAD_REQUEST);
        }

        // Check if email already exists
        $existingUser = $this->entityManager->getRepository(CustomerUser::class)
            ->findOneBy(['email' => $data['email']]);
        
        if ($existingUser) {
            return $this->json(['error' => 'Email already registered'], Response::HTTP_CONFLICT);
        }

        try {
            // Check preliminary ssoProvider to decide about password requirement
            $pre = null;
            if (!empty($data['token'])) {
                $pre = $this->entityManager->getRepository(PreliminaryRegistration::class)->findOneBy(['token' => $data['token']]);
            }

            // Create Customer (Company)
            $customer = new Customer();
            if (!empty($data['companyName'])) {
                $customer->setCompanyName($data['companyName']);
            } else {
                $customer->setCompanyName('');
            }
            $customer->setType($data['customerType']) // 'individual' or 'business'
                    ->setStatus('active');

            // Set optional company fields if provided
            if (!empty($data['vatNumber'])) {
                $customer->setVatNumber($data['vatNumber']);
            }
            if (!empty($data['regon'])) {
                $customer->setRegon($data['regon']);
            }
            if (!empty($data['address'])) {
                $customer->setAddress($data['address']);
            }
            if (!empty($data['postalCode'])) {
                $customer->setPostalCode($data['postalCode']);
            }
            if (!empty($data['city'])) {
                $customer->setCity($data['city']);
            }
            if (!empty($data['country'])) {
                $customer->setCountry($data['country']);
            }
            if (!empty($data['phone'])) {
                $customer->setPhone($data['phone']);
            }
            if (!empty($data['email'])) {
                $customer->setEmail($data['email']);
            }

            // Create Customer User (First user - Owner)
            $customerUser = new CustomerUser();
            $customerUser->setEmail($data['email'])
                        ->setFirstName($data['firstName'])
                        ->setLastName($data['lastName'])
                        ->setCustomerRole('owner') // First user is always owner
                        ->setStatus('active')
                        ->setCustomer($customer);

            // Hash password (if SSO, generate a strong random password)
            $rawPassword = $data['password'] ?? null;
            if (!$rawPassword && $pre && $pre->getSsoProvider()) {
                $rawPassword = bin2hex(random_bytes(12));
            }
            if (!$rawPassword) {
                return $this->json(['error' => 'Password is required'], Response::HTTP_BAD_REQUEST);
            }
            $hashedPassword = $this->passwordHasher->hashPassword($customerUser, $rawPassword);
            $customerUser->setPassword($hashedPassword);

            $userPhone = $data['userPhone'] ?? ($data['phone'] ?? null);
            if (!empty($userPhone)) { $customerUser->setPhone($userPhone); }

            // Validate entities
            $customerErrors = $this->validator->validate($customer);
            $userErrors = $this->validator->validate($customerUser);

            if (count($customerErrors) > 0 || count($userErrors) > 0) {
                $errors = [];
                foreach ($customerErrors as $error) {
                    $errors[] = $error->getMessage();
                }
                foreach ($userErrors as $error) {
                    $errors[] = $error->getMessage();
                }
                return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Save to database
            $this->entityManager->persist($customer);
            $this->entityManager->persist($customerUser);
            $this->entityManager->flush();

            // Mark preliminary registration as completed if token provided
            if ($pre) { $pre->setStatus('completed')->setUpdatedAt(new \DateTime()); }

            return $this->json([
                'message' => 'Registration successful',
                'customer' => [
                    'id' => $customer->getId(),
                    'companyName' => $customer->getCompanyName(),
                    'type' => $customer->getType()
                ],
                'user' => [
                    'id' => $customerUser->getId(),
                    'email' => $customerUser->getEmail(),
                    'fullName' => $customerUser->getFullName(),
                    'role' => $customerUser->getCustomerRole()
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/gus-lookup', name: 'api_gus_lookup', methods: ['POST'])]
    public function gusLookup(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $nip = preg_replace('/[^0-9]/', '', $data['nip'] ?? '');
        $country = strtoupper($data['country'] ?? 'PL');
        $unregistered = (bool)($data['unregisteredBusiness'] ?? false);
        if ($country !== 'PL' || $unregistered) {
            return $this->json(['skipped' => true]);
        }
        if (strlen($nip) !== 10) {
            return $this->json(['error' => 'Invalid NIP'], Response::HTTP_BAD_REQUEST);
        }
        // TODO: Integrate with BIR GUS (requires API key). Placeholder response for now.
        return $this->json([
            'status' => 'ok',
            'company' => [
                'name' => 'Przykładowa Sp. z o.o.',
                'address' => 'ul. Przykładowa 1, 00-001 Warszawa',
                'nip' => $nip,
                'active' => true
            ]
        ]);
    }

    #[Route('/check-email', name: 'api_check_email', methods: ['POST'])]
    public function checkEmail(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['email'])) {
            return $this->json(['error' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $this->entityManager->getRepository(CustomerUser::class)
            ->findOneBy(['email' => $data['email']]);

        return $this->json([
            'available' => $existingUser === null,
            'message' => $existingUser ? 'Email already registered' : 'Email available'
        ]);
    }

    #[Route('/customer-types', name: 'api_customer_types', methods: ['GET'])]
    public function getCustomerTypes(): JsonResponse
    {
        return $this->json([
            'types' => [
                [
                    'value' => 'individual',
                    'label' => 'Individual Customer',
                    'description' => 'Personal account for individual customers'
                ],
                [
                    'value' => 'business',
                    'label' => 'Business Customer', 
                    'description' => 'Company account for business customers'
                ]
            ]
        ]);
    }
}

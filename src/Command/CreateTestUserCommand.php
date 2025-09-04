<?php

namespace App\Command;

use App\Entity\Customer;
use App\Entity\CustomerUser;
use App\Entity\SystemUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-test-user',
    description: 'Create test users for authentication testing',
)]
class CreateTestUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Create test customer
            $customer = new Customer();
            $customer->setCompanyName('Test Company Ltd');
            $customer->setType('company');
            $customer->setStatus('active');
            $customer->setEmail('test@company.com');
            $customer->setPhone('+48123456789');
            $customer->setCreatedAt(new \DateTime());

            $this->entityManager->persist($customer);

            // Create test customer user
            $customerUser = new CustomerUser();
            $customerUser->setEmail('test@test.com');
            $customerUser->setFirstName('Test');
            $customerUser->setLastName('User');
            $customerUser->setPhone('+48123456789');
            $customerUser->setCustomerRole('owner');
            $customerUser->setStatus('active');
            $customerUser->setCreatedAt(new \DateTime());
            $customerUser->setCustomer($customer);
            $customerUser->setRoles(['ROLE_CUSTOMER_USER']);

            // Hash password
            $hashedPassword = $this->passwordHasher->hashPassword($customerUser, 'test123');
            $customerUser->setPassword($hashedPassword);

            $this->entityManager->persist($customerUser);

            // Create test system user
            $systemUser = new SystemUser();
            $systemUser->setEmail('admin@test.com');
            $systemUser->setFirstName('Admin');
            $systemUser->setLastName('User');
            $systemUser->setPhone('+48987654321');
            $systemUser->setDepartment('IT');
            $systemUser->setPosition('Administrator');
            // Note: setRole method doesn't exist, just use roles
            $systemUser->setStatus('active');
            $systemUser->setCreatedAt(new \DateTime());
            $systemUser->setRoles(['ROLE_SYSTEM_USER', 'ROLE_ADMIN']);

            // Hash password
            $hashedPassword = $this->passwordHasher->hashPassword($systemUser, 'admin123');
            $systemUser->setPassword($hashedPassword);

            $this->entityManager->persist($systemUser);

            $this->entityManager->flush();

            $io->success('Test users created successfully!');
            $io->table(
                ['Type', 'Email', 'Password'],
                [
                    ['Customer', 'test@test.com', 'test123'],
                    ['System', 'admin@test.com', 'admin123']
                ]
            );

        } catch (\Exception $e) {
            $io->error('Failed to create test users: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
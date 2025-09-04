<?php

namespace App\Command;

use App\Entity\SystemUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-system-user',
    description: 'Create a new system user',
)]
class CreateSystemUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addArgument('firstName', InputArgument::REQUIRED, 'First name')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Last name')
            ->addArgument('department', InputArgument::OPTIONAL, 'Department', 'admin')
            ->addArgument('position', InputArgument::OPTIONAL, 'Position', 'System Administrator')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $department = $input->getArgument('department');
        $position = $input->getArgument('position');

        // Check if user already exists
        $existingUser = $this->entityManager->getRepository(SystemUser::class)
            ->findOneBy(['email' => $email]);

        if ($existingUser) {
            $io->error('User with this email already exists!');
            return Command::FAILURE;
        }

        // Create system user
        $systemUser = new SystemUser();
        $systemUser->setEmail($email)
                  ->setFirstName($firstName)
                  ->setLastName($lastName)
                  ->setDepartment($department)
                  ->setPosition($position)
                  ->setStatus('active');

        // Hash password
        $hashedPassword = $this->passwordHasher->hashPassword($systemUser, $password);
        $systemUser->setPassword($hashedPassword);

        // Save to database
        $this->entityManager->persist($systemUser);
        $this->entityManager->flush();

        $io->success(sprintf(
            'System user created successfully! ID: %d, Email: %s, Department: %s',
            $systemUser->getId(),
            $systemUser->getEmail(),
            $systemUser->getDepartment()
        ));

        return Command::SUCCESS;
    }
}
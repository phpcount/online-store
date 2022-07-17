<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';
    protected static $defaultDescription = 'Create user';

    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(string $name = null, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository)
    {
        parent::__construct($name);
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('email', 'l', InputOption::VALUE_REQUIRED, 'Email')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password')
            ->addOption('role', '', InputOption::VALUE_OPTIONAL, 'Set role')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $role = $input->getOption('role');

        if (!$email) {
            $email = $io->ask('Email');
        }

        if (!$password) {
            $password = $io->askHidden('Password (your type will be hidden)');
        }

        if (!$role) {
            $role = $io->ask('Set role');
        }

        try {
            $user = $this->createUser($email, $password, $role);
        } catch (RuntimeException $e) {
            $io->warning($e->getMessage());

            return Command::FAILURE;
        }

        $successMessage = sprintf(
            '%s was successfully created: %s',
            in_array($role, ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']) ? 'Administrator user' : 'User',
            $email
        );
        $io->success($successMessage);

        $event = $stopwatch->stop('add-user-command');

        $stopwatchMessage = sprintf(
            'New user\'s id: %d / Elapsed time: %.2f ms / Consumed memory:  %.2f MB',
            $user->getId(),
            $event->getDuration(),
            $event->getMemory() / 1000000
        );
        $io->comment($stopwatchMessage);

        return Command::SUCCESS;
    }

    /**
     * Undocumented function.
     */
    private function createUser(string $email, string $password, string $role): User
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            throw new RuntimeException('User already exists');
        }

        $user = new User();
        $user
            ->setEmail($email)
            ->setRoles([$role])
            ->setIsVerified(true)
        ;

        $hashPassword = $this->userPasswordHasher->hashPassword($user, $password);
        $user->setPassword($hashPassword);

        $this->userRepository->add($user, true);

        return $user;
    }
}

<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';
    protected static $defaultDescription = 'Create user';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(string $name = null, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('email', 'l', InputOption::VALUE_REQUIRED, 'Email')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password')
            ->addOption('isAdmin', '', InputOption::VALUE_OPTIONAL, 'If set the user is created as an administrator', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        if (!$email) {
            $email = $io->ask('Email');
        }

        if (!$password) {
            $password = $io->askHidden('Password (your type will be hidden)');
        }

        if (!$isAdmin) {
            $question = new Question('Is admin? (yes or no)', 'no');
            $isAdmin = $io->askQuestion($question);
        }
        $isAdmin = $isAdmin === 1 || $isAdmin === 'y' || $isAdmin === 'yes';

        try {
            $user = $this->createUser($email, $password, $isAdmin);
        } catch (RuntimeException $e) {
            $io->warning($e->getMessage());

            return Command::FAILURE;
        }

        $successMessage = sprintf(
            "%s was successfully created: %s", 
            $isAdmin ? 'Administrator user' : 'User',
            $email
        );
        $io->success($successMessage);

        $event = $stopwatch->stop('add-user-command');

        $stopwatchMessage =  sprintf(
            'New user\'s id: %d / Elapsed time: %.2f ms / Consumed memory:  %.2f MB', 
            $user->getId(),
            $event->getDuration(),
            $event->getMemory() / 1000000
        );
        $io->comment($stopwatchMessage);

        return Command::SUCCESS;
    }

    /**
     * Undocumented function
     *
     * @param string $email
     * @param string $password
     * @param boolean $isAdmin
     * @return User
     */
    private function createUser(string $email,  string $password, bool $isAdmin): User
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            throw new RuntimeException('User already exists');
        }

        $user = new User();
        $user
            ->setEmail($email)
            ->setRoles([ $isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER'])
            ->setIsVerified(true)
        ;

        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setPassword($encodedPassword);
        $this->userRepository->add($user, true);

        return $user;
    }

}

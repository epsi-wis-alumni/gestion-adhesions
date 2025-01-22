<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:make:user',
    description: 'Add a short description for your command',
)]
class MakeUserCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserManager $userManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $firstname = $io->ask('What is the user\'s first name?', null, function (string $answer): string {
            if (empty($answer)) {
                throw new \RuntimeException('The first name cannot be empty.');
            }

            return $answer;
        });

        $lastname = $io->ask('What is the user\'s last name?', null, function (string $answer): string {
            if (empty($answer)) {
                throw new \RuntimeException('The last name cannot be empty.');
            }

            return $answer;
        });

        $email = $io->ask('What is the user\'s email?', null, function (string $answer): string {
            if (empty($answer)) {
                throw new \RuntimeException('The email cannot be empty.');
            }

            if (filter_var($answer, FILTER_VALIDATE_EMAIL) === false) {
                throw new \RuntimeException('The email is not valid.');
            }

            return $answer;
        });

        $isAdmin = $io->confirm('Should the user be admin?', false);

        $user = new User();
        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
        ;

        if ($isAdmin) {
            $this->userManager->addRole($user, User::ROLE_ADMIN);
        }

        $this->userRepository->save($user, true);

        return Command::SUCCESS;
    }
}

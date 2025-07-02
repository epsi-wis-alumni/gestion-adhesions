<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:set-admin',
    description: 'Add a short description for your command',
)]
class UserSetAdminCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserManager $userManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask(
            'What is the email of the user you to make admin?',
            validator: fn (string $answer): string => '' === $answer || '0' === $answer ?
                throw new \RuntimeException('The email cannot be empty.') : $answer
        );

        /** @var User */
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error('User not found.');

            return Command::FAILURE;
        }

        $this->userManager->addRole($user, User::ROLE_ADMIN);
        $this->userRepository->save($user, flush: true);
        $io->success('The user has been made admin!');

        return Command::SUCCESS;
    }
}

<?php

namespace App\Command;

use App\Entity\Candidacy;
use App\Entity\Election;
use App\Entity\Vote;
use App\Repository\ElectionRepository;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:election:results',
    description: 'Display results for a given election',
)]
class ElectionResultsCommand extends Command
{
    public function __construct(
        protected ElectionRepository $electionRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('electionId', InputArgument::OPTIONAL, 'The ID of the wanted election')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $electionId = $input->getArgument('electionId');

        if (!$electionId) {
            $elections = $this->electionRepository->findDone();
            $choices = array_map(
                fn (Election $election) => implode(' ', ['#'.$election->getId(), $election->getVoteEndAt()->format('Y-m-d'), $election->getJobTitle()]),
                $elections
            );
            $selected = $io->choice('Which election do you wish to display results?', $choices);
            $electionId = $elections[array_search($selected, $choices)]->getId();
        }

        /** @var Election */
        $election = $this->electionRepository->find($electionId);
        $io->success('Selected election #'.$election->getId());

        $io->title('Results by candiate');
        $io->table(
            ['ID', 'Name', 'Votes', '%'],
            $election->getCandidacys()->map(fn (Candidacy $candidacy) => [
                $candidacy->getId(),
                $candidacy->getCandidacy()->getDisplayName(),
                $candidacy->getVotes()->count(),
                'test'
            ])->toArray(),
        );

        /** @var Candidacy */
        $candidacy = $election->getCandidacys()->first();

        $io->title('Winner\'s votes');
        $io->table(
            ['ID', 'Name', 'Voted At'],
            $candidacy->getVotes()->map(fn (Vote $vote) => [
                $vote->getVoter()->getId(),
                $vote->getVoter()->getDisplayName(),
                $vote->getVotedAt()->format(DateTimeImmutable::ATOM),
            ])->toArray(),
        );

        return Command::SUCCESS;
    }
}

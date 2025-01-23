<?php

namespace App\DataFixtures;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Service\UserManager;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        protected UserManager $userManager,
    )
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        // USERS

        $userAdmin = new User();
        $userAdmin
            ->setFirstname('Admin')
            ->setLastname('ADMIN')
            ->setEmail('admin@gmail.com')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        // $this->userManager->approve($userAdmin, $userAdmin);
        $this->userManager->addRole($userAdmin, 'ROLE_ADMIN');
        $manager->persist($userAdmin);
        $manager->flush();

        $userCandidate1 = new User();
        $userCandidate1
            ->setFirstname('Candidate1')
            ->setLastname('CANDIDATE1')
            ->setEmail('candidate1@gmail.com')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        $this->userManager->approve($userCandidate1, $userAdmin);
        

        $userCandidate2 = new User();
        $userCandidate2
            ->setFirstname('Candidate2')
            ->setLastname('CANDIDATE2')
            ->setEmail('candidate2@gmail.com')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        $this->userManager->approve($userCandidate2, $userAdmin);

        $userVoter1 = new User();
        $userVoter1
            ->setFirstname('Voter1')
            ->setLastname('VOTER1')
            ->setEmail('voter1@gmail.com')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        $this->userManager->approve($userVoter1, $userAdmin);

        $manager->persist($userCandidate1);
        $manager->persist($userCandidate2);
        $manager->persist($userVoter1);
        
        $manager->flush();

        // ELECTIONS

        $today = new DateTimeImmutable();
        $yesterday = $today->modify('-1 day');
        $tomorrow = $today->modify('+1 day');

        $election = new Election();
        $election
            ->setCreatedAt($yesterday)
            ->setCreatedBy($userAdmin)

            ->setJobTitle('Secrétaire')
            ->setVoteStartAt($yesterday)
            ->setVoteEndAt($tomorrow)
        ;

        $manager->persist($election);
        
        $manager->flush();

        // CANDIDATES

        $candidate1 = new Candidate();
        $candidate1
            ->setCandidate($userCandidate1)
            ->setCandidatedAt($today)
            ->setElection($election)
            ->setPresentation('Je me présente')
        ;

        $candidate2 = new Candidate();
        $candidate2
            ->setCandidate($userCandidate2)
            ->setCandidatedAt($today)
            ->setElection($election)
            ->setPresentation('Je me présente 2')
        ;

        $manager->persist($candidate1);
        $manager->persist($candidate2);
        
        $manager->flush();

        // VOTES

        $vote1 = new Vote();
        $vote1
            ->setCandidate($candidate1)
            ->setElection($election)
            ->setVoter($userCandidate1)
            ->setVotedAt($today)
        ;

        $vote2 = new Vote();
        $vote2
            ->setCandidate($candidate1)
            ->setElection($election)
            ->setVoter($userCandidate2)
            ->setVotedAt($today)
        ;

        $vote3 = new Vote();
        $vote3
            ->setCandidate($candidate2)
            ->setElection($election)
            ->setVoter($userAdmin)
            ->setVotedAt($today)
        ;

        $vote4 = new Vote();
        $vote4
            ->setCandidate($candidate2)
            ->setElection($election)
            ->setVoter($userVoter1)
            ->setVotedAt($today)
        ;

        $manager->persist($vote1);
        $manager->persist($vote2);
        $manager->persist($vote3);
        $manager->persist($vote4);

        $manager->flush();
    }
}

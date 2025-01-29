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

/**
 * Reload the database with some data
 * symfony console doctrine:database:drop --force && symfony console doctrine:database:create && symfony console d:m:m -n && symfony console doctrine:fixtures:load -n
 */

class AppFixtures extends Fixture
{
    public function __construct(
        protected UserManager $userManager,
    ) {}

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        
        // USERS

        $userPerso = new User();
        $userPerso
            ->setFirstname($_ENV['USER_FIRSTNAME'])
            ->setLastname($_ENV['USER_LASTNAME'])
            ->setEmail($_ENV['USER_EMAIL'])
            ->setGoogleId($_ENV['USER_GOOGLE_ID'])
            ->setAvatar($_ENV['USER_AVATAR'])
            ->setCompany('EPSI')
            ->setJobTitle('Secrétaire')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        $this->userManager->addRole($userPerso, 'ROLE_ADMIN');

        $userAdmin = new User();
        $userAdmin
            ->setFirstname('Admin')
            ->setLastname('ADMIN')
            ->setEmail('admin@gmail.com')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        // $this->userManager->approve($userAdmin, $userAdmin);
        $this->userManager->addRole($userAdmin, 'ROLE_ADMIN');

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

        $manager->persist($userPerso);
        $manager->persist($userAdmin);
        $manager->persist($userCandidate1);
        $manager->persist($userCandidate2);
        $manager->persist($userVoter1);
        
        $manager->flush();

        // ELECTIONS

        $today = new DateTimeImmutable();
        $yesterday = $today->modify('-1 day');
        $tomorrow = $today->modify('+1 day');

        $election1 = new Election();
        $election1
            ->setCreatedAt($yesterday)
            ->setCreatedBy($userAdmin)

            ->setJobTitle('Secrétaire')
            ->setVoteStartAt($yesterday)
            ->setVoteEndAt($yesterday->modify('+1 hour'))
        ;

        $election2 = new Election();
        $election2
            ->setCreatedAt($yesterday)
            ->setCreatedBy($userAdmin)

            ->setJobTitle('Trésorier')
            ->setVoteStartAt($today)
            ->setVoteEndAt($today->modify('next day midnight -1 minute'))
        ;

        $election3 = new Election();
        $election3
            ->setCreatedAt($yesterday)
            ->setCreatedBy($userAdmin)

            ->setJobTitle('Président')
            ->setVoteStartAt($tomorrow)
            ->setVoteEndAt($tomorrow->modify('next day midnight -1 minute'))
        ;

        $manager->persist($election1);
        $manager->persist($election2);
        $manager->persist($election3);
        
        $manager->flush();

        // CANDIDATES

        $candidate1 = new Candidate();
        $candidate1
            ->setCandidate($userCandidate1)
            ->setCandidatedAt($today)
            ->setElection($election1)
            ->setPresentation('Je me présente')
        ;

        $candidate2 = new Candidate();
        $candidate2
            ->setCandidate($userCandidate2)
            ->setCandidatedAt($today)
            ->setElection($election1)
            ->setPresentation('Je me présente 2')
        ;

        $manager->persist($candidate1);
        $manager->persist($candidate2);
        
        $manager->flush();

        // VOTES

        $vote1 = new Vote();
        $vote1
            ->setCandidate($candidate1)
            ->setElection($election1)
            ->setVoter($userCandidate1)
            ->setVotedAt($today)
        ;

        $vote2 = new Vote();
        $vote2
            ->setCandidate($candidate1)
            ->setElection($election1)
            ->setVoter($userCandidate2)
            ->setVotedAt($today)
        ;

        $vote3 = new Vote();
        $vote3
            ->setCandidate($candidate2)
            ->setElection($election1)
            ->setVoter($userAdmin)
            ->setVotedAt($today)
        ;

        $vote4 = new Vote();
        $vote4
            ->setCandidate($candidate2)
            ->setElection($election1)
            ->setVoter($userVoter1)
            ->setVotedAt($today)
        ;

        $vote5 = new Vote();
        $vote5
            ->setCandidate($candidate2)
            ->setElection($election1)
            ->setVoter($userPerso)
            ->setVotedAt($today)
        ;

        $manager->persist($vote1);
        $manager->persist($vote2);
        $manager->persist($vote3);
        $manager->persist($vote4);
        $manager->persist($vote5);

        $manager->flush();
    }
}

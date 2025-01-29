<?php

namespace App\DataFixtures;

use App\Entity\Candidacy;
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

        $userCandidacy1 = new User();
        $userCandidacy1
            ->setFirstname('Candidacy1')
            ->setLastname('CANDIDATE1')
            ->setEmail('candidacy1@gmail.com')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        $this->userManager->approve($userCandidacy1, $userAdmin);
        

        $userCandidacy2 = new User();
        $userCandidacy2
            ->setFirstname('Candidacy2')
            ->setLastname('CANDIDATE2')
            ->setEmail('candidacy2@gmail.com')
            ->setCreatedAt(new DateTimeImmutable())
        ;
        $this->userManager->approve($userCandidacy2, $userAdmin);

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
        $manager->persist($userCandidacy1);
        $manager->persist($userCandidacy2);
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

        $candidacy1 = new Candidacy();
        $candidacy1
            ->setCandidate($userCandidacy1)
            ->setCandidacydAt($today)
            ->setElection($election1)
            ->setPresentation('Je me présente')
        ;

        $candidacy2 = new Candidacy();
        $candidacy2
            ->setCandidate($userCandidacy2)
            ->setCandidacydAt($today)
            ->setElection($election1)
            ->setPresentation('Je me présente 2')
        ;

        $manager->persist($candidacy1);
        $manager->persist($candidacy2);
        
        $manager->flush();

        // VOTES

        $vote1 = new Vote();
        $vote1
            ->setCandidacy($candidacy1)
            ->setElection($election1)
            ->setVoter($userCandidacy1)
            ->setVotedAt($today)
        ;

        $vote2 = new Vote();
        $vote2
            ->setCandidacy($candidacy1)
            ->setElection($election1)
            ->setVoter($userCandidacy2)
            ->setVotedAt($today)
        ;

        $vote3 = new Vote();
        $vote3
            ->setCandidacy($candidacy2)
            ->setElection($election1)
            ->setVoter($userAdmin)
            ->setVotedAt($today)
        ;

        $vote4 = new Vote();
        $vote4
            ->setCandidacy($candidacy2)
            ->setElection($election1)
            ->setVoter($userVoter1)
            ->setVotedAt($today)
        ;

        $vote5 = new Vote();
        $vote5
            ->setCandidacy($candidacy2)
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

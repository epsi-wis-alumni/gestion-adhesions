<?php

namespace App\DataFixtures;

use App\Entity\Candidacy;
use App\Entity\Election;
use App\Entity\Event;
use App\Entity\Feature;
use App\Entity\MailTemplate;
use App\Entity\Newsletter;
use App\Entity\Plan;
use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Vote;
use App\Enum\TransactionStatus;
use App\Service\UserManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Reload the database with some data
 * symfony console doctrine:database:drop --force && symfony console doctrine:database:create && symfony console d:s:u --force -n && symfony console doctrine:fixtures:load -n.
 */
class AppFixtures extends Fixture
{
    public function __construct(
        protected UserManager $userManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

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
        ;
        $this->userManager->addRole($userPerso, 'ROLE_ADMIN');

        $userAdmin = new User();
        $userAdmin
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setEmail($faker->email())
        ;
        // $this->userManager->approve($userAdmin, $userAdmin);
        $this->userManager->addRole($userAdmin, 'ROLE_ADMIN');

        $userCandidacy1 = new User();
        $userCandidacy1
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setEmail($faker->email())
        ;
        $this->userManager->approve($userCandidacy1, $userAdmin);

        $userCandidacy2 = new User();
        $userCandidacy2
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setEmail($faker->email())
        ;
        $this->userManager->approve($userCandidacy2, $userAdmin);

        $userVoter1 = new User();
        $userVoter1
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setEmail($faker->email())
        ;
        $this->userManager->approve($userVoter1, $userAdmin);

        $manager->persist($userPerso);
        $manager->persist($userAdmin);
        $manager->persist($userCandidacy1);
        $manager->persist($userCandidacy2);
        $manager->persist($userVoter1);

        $manager->flush();

        // ELECTIONS

        $today = new \DateTimeImmutable();
        $yesterday = $today->modify('-1 day');
        $tomorrow = $today->modify('+1 day');

        $election1 = new Election();
        $election1
            ->setCreatedBy($userAdmin)
            ->setJobTitle('Secrétaire')
            ->setVoteStartAt($yesterday)
            ->setVoteEndAt($yesterday->modify('+1 hour'))
        ;

        $election2 = new Election();
        $election2
            ->setCreatedBy($userAdmin)
            ->setJobTitle('Trésorier')
            ->setVoteStartAt($today)
            ->setVoteEndAt($today->modify('next day midnight -1 minute'))
        ;

        $election3 = new Election();
        $election3
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

        // PLANS

        $plan1 = new Plan();
        $plan1
            ->setName('Alumni')
            ->setDescription("L'abonnement spécial ancien élève")
            ->setPrice(15.00)
        ;

        $plan2 = new Plan();
        $plan2
            ->setName('Grand Prince')
            ->setDescription("L'abonnement spécial donnateur")
            ->setPrice(60.00)
            ->setHighlighted(true)
        ;

        $plan3 = new Plan();
        $plan3
            ->setName('Grande Pince')
            ->setDescription("L'abonnement spécial étudiant")
            ->setPrice(5.00)
        ;

        $manager->persist($plan1);
        $manager->persist($plan2);
        $manager->persist($plan3);

        $manager->flush();

        // FEATURES
        // 1 - Plan 1
        $feature1_1 = new Feature();
        $feature1_1
            ->setName('Accès au réseau des anciens élèves')
            ->setPlan($plan1)
        ;

        $feature1_2 = new Feature();
        $feature1_2
            ->setName('Participation aux événements')
            ->setPlan($plan1)
        ;

        // 2 - Plan 2
        $feature2_1 = new Feature();
        $feature2_1
            ->setName('Mention honorifique')
            ->setPlan($plan2)
        ;

        $feature2_2 = new Feature();
        $feature2_2
            ->setName('Accès premium aux événements')
            ->setPlan($plan2)
        ;

        $feature2_3 = new Feature();
        $feature2_3
            ->setName('Consultations personnalisées')
            ->setPlan($plan2)
        ;

        $feature2_4 = new Feature();
        $feature2_4
            ->setName('Contenus exclusifs')
            ->setPlan($plan2)
        ;

        $feature2_5 = new Feature();
        $feature2_5
            ->setName('Opportunité de parrainage')
            ->setPlan($plan2)
        ;

        $feature2_6 = new Feature();
        $feature2_6
            ->setName('Badge spécial')
            ->setPlan($plan2)
        ;

        // 3 - Plan 3
        $feature3_1 = new Feature();
        $feature3_1
            ->setName('Mises à jour professionnelles')
            ->setPlan($plan3)
        ;

        $feature3_2 = new Feature();
        $feature3_2
            ->setName('Accès illimité à la base de données des membres')
            ->setPlan($plan3)
        ;

        $manager->persist($feature1_1);
        $manager->persist($feature1_2);
        $manager->persist($feature2_1);
        $manager->persist($feature2_2);
        $manager->persist($feature2_3);
        $manager->persist($feature2_4);
        $manager->persist($feature2_5);
        $manager->persist($feature2_6);
        $manager->persist($feature3_1);
        $manager->persist($feature3_2);

        $manager->flush();

        // MAIL_TEMPLATE

        $mailTemplate1 = new MailTemplate();
        $mailTemplate1
            ->setLabel('Élection')
            ->setFileName('election.html.twig')
        ;

        $mailTemplate2 = new MailTemplate();
        $mailTemplate2
            ->setLabel('Évènement')
            ->setFileName('event.html.twig')
        ;

        $mailTemplate3 = new MailTemplate();
        $mailTemplate3
            ->setLabel('Newsletter')
            ->setFileName('newsletter.html.twig')
        ;

        $manager->persist($mailTemplate1);
        $manager->persist($mailTemplate2);
        $manager->persist($mailTemplate3);

        $manager->flush();

        // NEWSLETTER

        $newsletter1 = new Newsletter();
        $newsletter1
            ->setCreatedBy($userAdmin)
            ->setTemplate($mailTemplate3)
            ->setObject('Découvrez nos plans adaptés à vos besoins !')
            ->setBody("
                Bonjour {{ userNewsletter.user.firstname }},

                Nous avons conçu des plans spécialement pour répondre à vos besoins. Que vous soyez étudiant, professionnel ou une entreprise, nous avons une solution pour vous !

                Plan Étudiant : Accès premium à 5€/mois seulement.
                Plan Pro : Optimisé pour les freelances à 10€/mois.
                Plan Entreprise : Une offre sur mesure pour gérer votre activité.

                Profitez-en maintenant et améliorez votre expérience !

                L'équipe EPSI-WIS Alumni.
            ")
            ->setCta('Découvrez nos offres')
        ;

        $newsletter2 = new Newsletter();
        $newsletter2
            ->setCreatedBy($userAdmin)
            ->setTemplate($mailTemplate3)
            ->setObject('Participez à notre prochain webinaire gratuit !')
            ->setBody("
                Bonjour {{ userNewsletter.user.firstname }},

                Rejoignez-nous pour un webinaire exclusif sur le thème : \"Comment maximiser les avantages de votre plan ?\"

                Date : Mardi 25 février 2025
                Heure : 18h00 (CET)
                Lieu : En ligne (lien envoyé après inscription)

                Ce que vous apprendrez :

                Optimiser l'utilisation des outils inclus dans votre plan.
                Découvrir les nouveautés 2025.
                Répondre à vos questions en direct avec notre équipe.

                Ne manquez pas cette opportunité !

                L'équipe EPSI-WIS Alumni.
            ")
            ->setCta('Inscrivez-vous gratuitement')
        ;

        $newsletter3 = new Newsletter();
        $newsletter3
            ->setCreatedBy($userAdmin)
            ->setTemplate($mailTemplate3)
            ->setObject('Votre plateforme évolue ! Découvrez les nouveautés.')
            ->setBody("
                Bonjour {{ userNewsletter.user.firstname }},

                Nous avons le plaisir de vous annoncer des nouveautés pour améliorer votre expérience :

                Nouvelle interface : Plus intuitive et rapide.
                Fonctionnalités avancées : Une gestion simplifiée de vos abonnements.
                Offres exclusives : Des remises pour les abonnés annuels.

                Merci pour votre confiance. Nous restons à votre disposition pour toute question ou suggestion.

                L'équipe EPSI-WIS Alumni.
            ")
            ->setCta('Explorez les nouveautés')
        ;

        $manager->persist($newsletter1);
        $manager->persist($newsletter2);
        $manager->persist($newsletter3);

        $manager->flush();

        // Évènements

        $evenement1 = new Event();
        $evenement1
            ->setCreatedBy($userAdmin)
            ->setTitle('Hackathon des Innovateurs')
            ->setPlace('349 Rue de la Cavalade, 34070 Montpellier')
            ->setStartAt(new \DateTimeImmutable('2025-03-15 10:00:00'))
            ->setEndAt(new \DateTimeImmutable('2025-03-17 18:00:00'))
            ->setPrivate(false)
        ;

        $evenement2 = new Event();
        $evenement2
            ->setCreatedBy($userAdmin)
            ->setTitle("Conférence : L'Intelligence Artificielle et l'Éthique")
            ->setPlace('349 Rue de la Cavalade, 34070 Montpellier')
            ->setStartAt(new \DateTimeImmutable('2025-04-23 09:30:00'))
            ->setEndAt(new \DateTimeImmutable('2025-04-23 17:00:00'))
            ->setPrivate(false)
        ;

        $evenement3 = new Event();
        $evenement3
            ->setCreatedBy($userAdmin)
            ->setTitle('Atelier de Création de Startups')
            ->setPlace('349 Rue de la Cavalade, 34070 Montpellier')
            ->setStartAt(new \DateTimeImmutable('2025-06-01 08:30:00'))
            ->setEndAt(new \DateTimeImmutable('2025-06-01 16:00:00'))
            ->setPrivate(true)
        ;

        $manager->persist($evenement1);
        $manager->persist($evenement2);
        $manager->persist($evenement3);

        $manager->flush();

        // SUBSCRIPTION

        $subscription1 = new Subscription();
        $subscription1
            ->setPlan($plan1)
            ->setDiscount(0)
        ;

        $subscription2 = new Subscription();
        $subscription2
            ->setPlan($plan2)
            ->setDiscount(0)
        ;
        $subscription3 = new Subscription();
        $subscription3
            ->setPlan($plan3)
            ->setDiscount(0)
        ;
        $manager->persist($subscription1);
        $manager->persist($subscription2);
        $manager->persist($subscription3);

        $manager->flush();

        // TRANSACTION

        $transaction1 = new Transaction();
        $transaction1
            ->setUser($userPerso)
            ->setSubscription($subscription1)
            ->setStatus(TransactionStatus::Completed)
            ->setType(1)
            ->setAmount(5.00)
            ->setRenewal(false)
            ->setCreatedAt()
        ;

        $transaction2 = new Transaction();
        $transaction2
            ->setUser($userAdmin)
            ->setSubscription($subscription2)
            ->setStatus(TransactionStatus::Completed)
            ->setType(1)
            ->setAmount(15.00)
            ->setRenewal(false)
            ->setCreatedAt()
        ;

        $transaction3 = new Transaction();
        $transaction3
            ->setUser($userCandidacy1)
            ->setSubscription($subscription3)
            ->setStatus(TransactionStatus::Completed)
            ->setType(1)
            ->setAmount(60.00)
            ->setRenewal(false)
            ->setCreatedAt()
        ;

        $manager->persist($transaction1);
        $manager->persist($transaction2);
        $manager->persist($transaction3);

        $manager->flush();
    }
}

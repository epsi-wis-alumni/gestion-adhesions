<?php

namespace App\DataFixtures;

use App\Entity\Candidacy;
use App\Entity\Category;
use App\Entity\Donation;
use App\Entity\Election;
use App\Entity\Event;
use App\Entity\Feature;
use App\Entity\JobOffer;
use App\Entity\MailTemplate;
use App\Entity\Newsletter;
use App\Entity\Plan;
use App\Entity\Skill;
use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Vote;
use App\Enum\JobOfferStatus;
use App\Enum\JobOfferType;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
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
            ->setpriceVariable(false)
        ;

        $plan2 = new Plan();
        $plan2
            ->setName('Grand Prince')
            ->setDescription("L'abonnement spécial donnateur")
            ->setPrice(50.00)
            ->setHighlighted(true)
            ->setpriceVariable(true)
        ;

        $plan3 = new Plan();
        $plan3
            ->setName('Grande Pince')
            ->setDescription("L'abonnement spécial étudiant")
            ->setPrice(5.00)
            ->setpriceVariable(false)
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
            ->setImageFileName("")
            ->setDescription(
"La conférence \"L'Intelligence Artificielle et l'Éthique\" explore 
les enjeux fondamentaux de l'IA dans notre société. Elle réunit 
des experts de renommée mondiale, chercheurs, philosophes, et 
professionnels pour débattre des dilemmes éthiques liés au 
développement et à l’utilisation de cette technologie.

L'événement propose une série de présentations, de tables rondes 
et de débats interactifs pour éclairer des questions essentielles. 
Quels sont les impacts de l’IA sur la vie privée ? Comment prévenir 
les discriminations algorithmiques ? Quelle responsabilité attribuer 
aux systèmes intelligents ? Ces problématiques seront abordées 
avec profondeur et pragmatisme.

Les participants auront l’opportunité d’interagir directement avec 
les intervenants pour poser des questions et partager leurs points 
de vue.

Cette conférence s’adresse à un large public : professionnels de 
la technologie, décideurs, chercheurs, étudiants et citoyens 
curieux des défis posés par l'IA.

Rejoignez-nous pour une réflexion commune sur la manière d’intégrer 
l’intelligence artificielle dans notre société, tout en respectant 
les valeurs fondamentales et en assurant un avenir responsable et 
équitable."
            );

        $evenement2 = new Event();
        $evenement2
            ->setCreatedBy($userAdmin)
            ->setTitle("Conférence : L'Intelligence Artificielle et l'Éthique")
            ->setPlace('349 Rue de la Cavalade, 34070 Montpellier')
            ->setStartAt(new \DateTimeImmutable('2025-04-23 09:30:00'))
            ->setEndAt(new \DateTimeImmutable('2025-04-23 17:00:00'))
            ->setPrivate(false)
            ->setImageFileName("")
            ->setDescription(
"La conférence \"L'Intelligence Artificielle et l'Éthique\" explore 
les enjeux fondamentaux de l'IA dans notre société. Elle réunit 
des experts de renommée mondiale, chercheurs, philosophes, et 
professionnels pour débattre des dilemmes éthiques liés au 
développement et à l’utilisation de cette technologie.

L'événement propose une série de présentations, de tables rondes 
et de débats interactifs pour éclairer des questions essentielles. 
Quels sont les impacts de l’IA sur la vie privée ? Comment prévenir 
les discriminations algorithmiques ? Quelle responsabilité attribuer 
aux systèmes intelligents ? Ces problématiques seront abordées 
avec profondeur et pragmatisme.

Les participants auront l’opportunité d’interagir directement avec 
les intervenants pour poser des questions et partager leurs points 
de vue. 

Cette conférence s’adresse à un large public : professionnels de 
la technologie, décideurs, chercheurs, étudiants et citoyens 
curieux des défis posés par l'IA.

Rejoignez-nous pour une réflexion commune sur la manière d’intégrer 
l’intelligence artificielle dans notre société, tout en respectant 
les valeurs fondamentales et en assurant un avenir responsable et 
équitable."
            );

        $evenement3 = new Event();
        $evenement3
            ->setCreatedBy($userAdmin)
            ->setTitle('Atelier de Création de Startups')
            ->setPlace('349 Rue de la Cavalade, 34070 Montpellier')
            ->setStartAt(new \DateTimeImmutable('2025-06-01 08:30:00'))
            ->setEndAt(new \DateTimeImmutable('2025-06-01 16:00:00'))
            ->setPrivate(true)
            ->setImageFileName("")
            ->setDescription(
"L'Atelier de Création de Startups est une expérience immersive 
conçue pour transformer des idées en entreprises concrètes. Cet 
atelier s’adresse aux aspirants entrepreneurs, étudiants, 
professionnels et passionnés souhaitant découvrir les bases de 
l’entrepreneuriat.

Sur une journée ou un week-end, les participants forment des 
équipes et suivent un processus structuré pour développer leurs 
projets. À travers des étapes clés, comme l’identification des 
problèmes, l’élaboration d’un business model, et la création d’un 
prototype, cet atelier fournit un cadre pratique et stimulant.

Des mentors expérimentés, issus de divers secteurs, sont présents 
pour guider les participants et partager leurs conseils. Ils aident 
à surmonter les obstacles, affiner les idées et comprendre les 
réalités du marché. L’atelier inclut également des sessions de 
pitch, où les équipes présentent leurs projets à un panel d’experts 
et reçoivent des retours constructifs.

Participer à cet atelier, c’est acquérir des compétences en 
entrepreneuriat, enrichir son réseau professionnel et, surtout, 
vivre l’excitation de créer une startup. C’est une porte d’entrée 
idéale pour se lancer dans l’aventure entrepreneuriale."
            );

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

        // DONATION

        $donation1 = new Donation();
        $donation1->setAmount(10.00);
        
        $donation2 = new Donation();
        $donation2->setAmount(20.00);
        
        $donation3 = new Donation();
        $donation3->setAmount(30.00);
        
        $manager->persist($donation1);
        $manager->persist($donation2);
        $manager->persist($donation3);

        $manager->flush();

        // TRANSACTION

        $transaction1 = new Transaction();
        $transaction1
            ->setUser($userPerso)
            ->setSubscription($subscription1)
            ->setStatus(TransactionStatus::Completed)
            ->setType(TransactionType::Subscription)
            ->setAmount(15.00)
            ->setRenewal(true)
            ->setSessionId('cs_test_a1I8X2WHH9k7ukfpHCfJI2AUNlzZLNKj9EQQsIChyBxJ7n9U9nGhJrPfFM')
        ;

        $transaction2 = new Transaction();
        $transaction2
            ->setUser($userAdmin)
            ->setSubscription($subscription2)
            ->setStatus(TransactionStatus::Completed)
            ->setType(TransactionType::Subscription)
            ->setAmount(5.00)
            ->setRenewal(true)
        ;

        $transaction3 = new Transaction();
        $transaction3
            ->setUser($userCandidacy1)
            ->setSubscription($subscription3)
            ->setStatus(TransactionStatus::Completed)
            ->setType(TransactionType::Subscription)
            ->setAmount(60.00)
            ->setRenewal(true)
        ;

        $transaction4 = new Transaction();
        $transaction4
            ->setUser($userPerso)
            ->setDonation($donation1)
            ->setStatus(TransactionStatus::Completed)
            ->setType(TransactionType::Donation)
            ->setAmount(12.50)
            ->setRenewal(false)
            ->setSessionId('cs_test_a1I8X2WHH9k7ukfpHCfJI2AUNlzZLNKj9EQQsIChyBxJ7n9U9nGhJrPfFM')
        ;

        $transaction5 = new Transaction();
        $transaction5
            ->setUser($userAdmin)
            ->setDonation($donation2)
            ->setStatus(TransactionStatus::Completed)
            ->setType(TransactionType::Donation)
            ->setAmount(amount: 1500.00)
            ->setRenewal(false)
        ;

        $transaction6 = new Transaction();
        $transaction6
            ->setUser($userCandidacy1)
            ->setDonation($donation3)
            ->setStatus(TransactionStatus::Completed)
            ->setType(TransactionType::Donation)
            ->setAmount(18657.00)
            ->setRenewal(false)
        ;

        $manager->persist($transaction1);
        $manager->persist($transaction2);
        $manager->persist($transaction3);
        $manager->persist($transaction4);
        $manager->persist($transaction5);
        $manager->persist($transaction6);

        $manager->flush();


        // Skill

        $skill1 = new Skill();
        $skill1
            ->setName("PHP Symfony")
        ;

        $skill2 = new Skill();
        $skill2
            ->setName("MySQL")
        ;

        $skill3 = new Skill();
        $skill3
            ->setName("Angular.js")
        ;
        
        $skill4 = new Skill();
        $skill4
            ->setName("Figma")
        ;

        $skill5 = new Skill();
        $skill5
            ->setName("PHP Symfony")
        ;
        
        $manager->persist($skill1);
        $manager->persist($skill2);
        $manager->persist($skill3);
        $manager->persist($skill4);
        $manager->persist($skill5);

        $manager->flush();

        // Category

        $category1 = new Category();
        $category1
            ->setName("Backend")
        ;

        $category2 = new Category();
        $category2
            ->setName("Gestion BDD")
        ;

        $category3 = new Category();
        $category3
            ->setName("Frontend")
        ;
        
        $category4 = new Category();
        $category4
            ->setName("Design / UX / UI")
        ;

        $category5 = new Category();
        $category5
            ->setName("Testing")
        ;
        
        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);
        $manager->persist($category5);

        $manager->flush();

        // JobOffer

        $jobOffer1 = new JobOffer();
        $jobOffer1
            ->setTitle("Développeur Backend Symfony / MySQL")
            ->setDescription("
Nous recherchons un Développeur Backend expérimenté pour rejoindre notre équipe dynamique et travailler sur des projets innovants. Vous serez en charge de concevoir, développer et maintenir des applications web robustes et évolutives en utilisant le framework Symfony et la base de données MySQL.

Vos missions :
Développer de nouvelles fonctionnalités et améliorer les solutions existantes.

Optimiser les performances des applications et des bases de données.

Collaborer avec les équipes Frontend et DevOps pour garantir la qualité et la stabilité des livrables.

Participer à la conception technique et au choix des architectures.

Profil recherché :
Maîtrise de Symfony et de MySQL.

Bonne compréhension des concepts d’API REST et de sécurité des applications web.

Capacité à travailler en équipe et à respecter les délais.

Rejoignez-nous et participez à des projets stimulants dans un environnement collaboratif !")
            ->setType(JobOfferType::CDI)
            ->setStartAt(new \DateTimeImmutable())
            ->setEndAt(null)
            ->setImageFilePath("/var/www/public/assets/jobOffer/images/dev_back_symfo.jpg")
            ->setStatus(JobOfferStatus::Online)
            ->addSkill($skill1)
            ->addSkill($skill2)
            ->addCategory($category1)
            ->addCategory($category2)
        ;

        $jobOffer2 = new JobOffer();
        $jobOffer2
            ->setTitle("Développeur Frontend Figma / Angular.js")
            ->setDescription("
Nous recherchons un Développeur Frontend talentueux et passionné pour transformer des maquettes Figma en interfaces web modernes et performantes en utilisant Angular.js. Vous jouerez un rôle clé dans la création d'expériences utilisateur intuitives et engageantes.

Vos missions :
Intégrer des maquettes Figma en composants dynamiques avec Angular.js.

Collaborer étroitement avec les équipes UX/UI pour garantir une fidélité parfaite aux designs.

Optimiser le code pour garantir des performances optimales et une compatibilité multi-navigateurs.

Mettre en œuvre les bonnes pratiques de développement, notamment en termes de tests, accessibilité et SEO.

Profil recherché :
Excellente maîtrise d'Angular.js et bonne compréhension des frameworks modernes.

Expérience avec Figma et la conversion de designs en code propre et maintenable.

Sens aigu du détail et des performances.

Rejoignez une équipe dynamique et contribuez à des projets innovants où vos idées feront la différence !")
            ->setType(JobOfferType::Internship)
            ->setStartAt(new \DateTimeImmutable())
            ->setEndAt(new \DateTimeImmutable("+ 2 month"))
            ->setImageFilePath("/var/www/public/assets/jobOffer/images/dev_front_angular.jpg")
            ->setStatus(JobOfferStatus::Online)
            ->addSkill($skill3)
            ->addSkill($skill4)
            ->addCategory($category3)
            ->addCategory($category4)
        ;

        $jobOffer3 = new JobOffer();
        $jobOffer3
            ->setTitle("Testeur Backend Symfony")
            ->setDescription("
Nous recherchons un Testeur Backend spécialisé en Symfony pour garantir la qualité et la fiabilité de nos applications. En collaboration avec les développeurs, vous serez chargé de concevoir et d'exécuter des tests afin d'identifier les anomalies, de valider les nouvelles fonctionnalités, et d'assurer des performances optimales.

Vos missions :
Concevoir des scénarios de test pour les fonctionnalités backend développées sous Symfony.

Mettre en œuvre des tests automatisés (unitaires, fonctionnels, API) et analyser les résultats.

Collaborer avec les développeurs pour identifier et corriger les bugs.

Documenter les processus de test et les résultats pour améliorer en continu la qualité du produit.

Profil recherché :
Bonne maîtrise de Symfony et des outils de test (PHPUnit, Behat, Postman).

Connaissance des bases de données et des APIs REST.

Sens du détail, rigueur et esprit critique.

Rejoignez notre équipe et jouez un rôle clé dans la fiabilité de nos solutions !")
            ->setType(JobOfferType::Apprenticeship)
            ->setStartAt(new \DateTimeImmutable())
            ->setEndAt(new \DateTimeImmutable("+ 1 year"))
            ->setImageFilePath("/var/www/public/assets/jobOffer/images/testing_symfo.jpg")
            ->setStatus(JobOfferStatus::Online)
            ->addSkill($skill5)
            ->addCategory($category5)
        ;

        $manager->persist($jobOffer1);
        $manager->persist($jobOffer2);
        $manager->persist($jobOffer3);

        $manager->flush();
    }
}

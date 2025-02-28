<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228130023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidacy CHANGE candidated_at candidated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE event CHANGE private private TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE plan CHANGE highlighted highlighted TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE discount discount INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE settings_newsletter_allowed settings_newsletter_allowed TINYINT(1) DEFAULT 0 NOT NULL, CHANGE settings_notifications_allowed settings_notifications_allowed TINYINT(1) DEFAULT 0 NOT NULL, CHANGE settings_election_notifications_allowed settings_election_notifications_allowed TINYINT(1) DEFAULT 0 NOT NULL, CHANGE settings_event_notifications_allowed settings_event_notifications_allowed TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE vote CHANGE voted_at voted_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidacy CHANGE candidated_at candidated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE event CHANGE private private TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE plan CHANGE highlighted highlighted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE subscription CHANGE discount discount INT NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE settings_newsletter_allowed settings_newsletter_allowed TINYINT(1) NOT NULL, CHANGE settings_notifications_allowed settings_notifications_allowed TINYINT(1) NOT NULL, CHANGE settings_election_notifications_allowed settings_election_notifications_allowed TINYINT(1) NOT NULL, CHANGE settings_event_notifications_allowed settings_event_notifications_allowed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE vote CHANGE voted_at voted_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}

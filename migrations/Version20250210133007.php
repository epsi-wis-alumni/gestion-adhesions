<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210133007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate (id INT AUTO_INCREMENT NOT NULL, candidate_id INT NOT NULL, election_id INT DEFAULT NULL, candidated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', presentation VARCHAR(255) NOT NULL, INDEX IDX_C8B28E4491BD8781 (candidate_id), INDEX IDX_C8B28E44A708DAFF (election_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE election (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, job_title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', vote_start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', vote_end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DCA03800B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, place VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', private TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3BAE0AA7B03A8386 (created_by_id), INDEX IDX_3BAE0AA7896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mail_template (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, sent_by_id INT DEFAULT NULL, template_id INT DEFAULT NULL, object VARCHAR(255) NOT NULL, body LONGTEXT NOT NULL, cta VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', send_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7E8585C8B03A8386 (created_by_id), INDEX IDX_7E8585C8A45BB98C (sent_by_id), INDEX IDX_7E8585C85DA0FB8 (template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, discount INT NOT NULL, periodicity INT NOT NULL, start_at DATE NOT NULL, end_at DATE DEFAULT NULL, features LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, subscription_id INT DEFAULT NULL, status INT NOT NULL, type INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_723705D1A76ED395 (user_id), INDEX IDX_723705D19A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, approved_by_id INT DEFAULT NULL, rejected_by_id INT DEFAULT NULL, deleted_by_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, job_title VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, microsoft_id VARCHAR(255) DEFAULT NULL, github_id VARCHAR(255) DEFAULT NULL, avatar VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', approved_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', rejected_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', settings_newsletter_allowed TINYINT(1) NOT NULL, settings_notifications_allowed TINYINT(1) NOT NULL, settings_election_notifications_allowed TINYINT(1) NOT NULL, INDEX IDX_8D93D6492D234F6A (approved_by_id), INDEX IDX_8D93D649CBF05FC9 (rejected_by_id), INDEX IDX_8D93D649C76F1F52 (deleted_by_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_newsletter (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id INT NOT NULL, newsletter_id INT NOT NULL, opened_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', sent_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D9E24324A76ED395 (user_id), INDEX IDX_D9E2432422DB1917 (newsletter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, voter_id INT DEFAULT NULL, election_id INT DEFAULT NULL, candidate_id INT DEFAULT NULL, voted_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A108564EBB4B8AD (voter_id), INDEX IDX_5A108564A708DAFF (election_id), INDEX IDX_5A10856491BD8781 (candidate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4491BD8781 FOREIGN KEY (candidate_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44A708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
        $this->addSql('ALTER TABLE election ADD CONSTRAINT FK_DCA03800B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8A45BB98C FOREIGN KEY (sent_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C85DA0FB8 FOREIGN KEY (template_id) REFERENCES mail_template (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6492D234F6A FOREIGN KEY (approved_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649CBF05FC9 FOREIGN KEY (rejected_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649C76F1F52 FOREIGN KEY (deleted_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_newsletter ADD CONSTRAINT FK_D9E24324A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_newsletter ADD CONSTRAINT FK_D9E2432422DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564EBB4B8AD FOREIGN KEY (voter_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856491BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E4491BD8781');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E44A708DAFF');
        $this->addSql('ALTER TABLE election DROP FOREIGN KEY FK_DCA03800B03A8386');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7B03A8386');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7896DBBDE');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8B03A8386');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8A45BB98C');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C85DA0FB8');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19A1887DC');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492D234F6A');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649CBF05FC9');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649C76F1F52');
        $this->addSql('ALTER TABLE user_newsletter DROP FOREIGN KEY FK_D9E24324A76ED395');
        $this->addSql('ALTER TABLE user_newsletter DROP FOREIGN KEY FK_D9E2432422DB1917');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564EBB4B8AD');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A708DAFF');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856491BD8781');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP TABLE election');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE mail_template');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_newsletter');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

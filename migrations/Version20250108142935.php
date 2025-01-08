<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250108142935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate (id INT AUTO_INCREMENT NOT NULL, candidate_id INT NOT NULL, election_id INT DEFAULT NULL, candidated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C8B28E4491BD8781 (candidate_id), INDEX IDX_C8B28E44A708DAFF (election_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE election (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, job_title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', vote_start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', vote_end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DCA03800B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE membership (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, company VARCHAR(255) NOT NULL, job_title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', approuved_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status INT NOT NULL, UNIQUE INDEX UNIQ_86FFD2859B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, sent_by_id INT DEFAULT NULL, objet VARCHAR(255) NOT NULL, body VARCHAR(255) NOT NULL, cta VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', send_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7E8585C8B03A8386 (created_by_id), INDEX IDX_7E8585C8A45BB98C (sent_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, allow_newsletters TINYINT(1) NOT NULL, allow_notifications TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_E545A0C5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, discount INT NOT NULL, periodicity INT NOT NULL, start_at DATE NOT NULL, end_at DATE DEFAULT NULL, features LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, subscription_id INT DEFAULT NULL, status INT NOT NULL, type INT NOT NULL, amount NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_723705D1A76ED395 (user_id), INDEX IDX_723705D19A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, approuved_by_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, job_title VARCHAR(255) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, microsoft_id VARCHAR(255) DEFAULT NULL, github_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992E67029 (approuved_by_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, voter_id INT DEFAULT NULL, election_id INT DEFAULT NULL, candidate_id INT DEFAULT NULL, voted_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A108564EBB4B8AD (voter_id), INDEX IDX_5A108564A708DAFF (election_id), INDEX IDX_5A10856491BD8781 (candidate_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4491BD8781 FOREIGN KEY (candidate_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44A708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
        $this->addSql('ALTER TABLE election ADD CONSTRAINT FK_DCA03800B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD2859B6B5FBA FOREIGN KEY (account_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8A45BB98C FOREIGN KEY (sent_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE settings ADD CONSTRAINT FK_E545A0C5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64992E67029 FOREIGN KEY (approuved_by_id) REFERENCES membership (id)');
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
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD2859B6B5FBA');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8B03A8386');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8A45BB98C');
        $this->addSql('ALTER TABLE settings DROP FOREIGN KEY FK_E545A0C5A76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1A76ED395');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19A1887DC');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64992E67029');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564EBB4B8AD');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A708DAFF');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856491BD8781');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP TABLE election');
        $this->addSql('DROP TABLE membership');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

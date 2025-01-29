<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129145840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856491BD8781');
        $this->addSql('CREATE TABLE candidacy (id INT AUTO_INCREMENT NOT NULL, candidacy_id INT NOT NULL, election_id INT DEFAULT NULL, candidacyd_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', presentation VARCHAR(255) NOT NULL, INDEX IDX_D930569D59B22434 (candidacy_id), INDEX IDX_D930569DA708DAFF (election_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569D59B22434 FOREIGN KEY (candidacy_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569DA708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E4491BD8781');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E44A708DAFF');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP INDEX IDX_5A10856491BD8781 ON vote');
        $this->addSql('ALTER TABLE vote CHANGE candidate_id candidacy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856459B22434 FOREIGN KEY (candidacy_id) REFERENCES candidacy (id)');
        $this->addSql('CREATE INDEX IDX_5A10856459B22434 ON vote (candidacy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856459B22434');
        $this->addSql('CREATE TABLE candidate (id INT AUTO_INCREMENT NOT NULL, candidate_id INT NOT NULL, election_id INT DEFAULT NULL, candidated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', presentation VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_C8B28E4491BD8781 (candidate_id), INDEX IDX_C8B28E44A708DAFF (election_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E4491BD8781 FOREIGN KEY (candidate_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44A708DAFF FOREIGN KEY (election_id) REFERENCES election (id)');
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569D59B22434');
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569DA708DAFF');
        $this->addSql('DROP TABLE candidacy');
        $this->addSql('DROP INDEX IDX_5A10856459B22434 ON vote');
        $this->addSql('ALTER TABLE vote CHANGE candidacy_id candidate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856491BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('CREATE INDEX IDX_5A10856491BD8781 ON vote (candidate_id)');
    }
}

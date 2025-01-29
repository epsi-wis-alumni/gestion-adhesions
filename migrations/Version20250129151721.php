<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129151721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569D59B22434');
        $this->addSql('DROP INDEX IDX_D930569D59B22434 ON candidacy');
        $this->addSql('ALTER TABLE candidacy CHANGE candidacy_id candidate_id INT NOT NULL, CHANGE candidacyd_at candidated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569D91BD8781 FOREIGN KEY (candidate_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_D930569D91BD8781 ON candidacy (candidate_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidacy DROP FOREIGN KEY FK_D930569D91BD8781');
        $this->addSql('DROP INDEX IDX_D930569D91BD8781 ON candidacy');
        $this->addSql('ALTER TABLE candidacy CHANGE candidate_id candidacy_id INT NOT NULL, CHANGE candidated_at candidacyd_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE candidacy ADD CONSTRAINT FK_D930569D59B22434 FOREIGN KEY (candidacy_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D930569D59B22434 ON candidacy (candidacy_id)');
    }
}

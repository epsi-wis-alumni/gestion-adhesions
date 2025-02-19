<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219135314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan_feature DROP FOREIGN KEY FK_A1683D6E60E4B879');
        $this->addSql('ALTER TABLE plan_feature DROP FOREIGN KEY FK_A1683D6EE899029B');
        $this->addSql('DROP TABLE plan_feature');
        $this->addSql('ALTER TABLE feature ADD plan_id INT NOT NULL');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD77566E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE INDEX IDX_1FD77566E899029B ON feature (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan_feature (plan_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_A1683D6E60E4B879 (feature_id), INDEX IDX_A1683D6EE899029B (plan_id), PRIMARY KEY(plan_id, feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE plan_feature ADD CONSTRAINT FK_A1683D6E60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plan_feature ADD CONSTRAINT FK_A1683D6EE899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD77566E899029B');
        $this->addSql('DROP INDEX IDX_1FD77566E899029B ON feature');
        $this->addSql('ALTER TABLE feature DROP plan_id');
    }
}

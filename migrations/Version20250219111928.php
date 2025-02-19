<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219111928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan_feature (plan_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_A1683D6EE899029B (plan_id), INDEX IDX_A1683D6E60E4B879 (feature_id), PRIMARY KEY(plan_id, feature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE plan_feature ADD CONSTRAINT FK_A1683D6EE899029B FOREIGN KEY (plan_id) REFERENCES plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plan_feature ADD CONSTRAINT FK_A1683D6E60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plan DROP features');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan_feature DROP FOREIGN KEY FK_A1683D6EE899029B');
        $this->addSql('ALTER TABLE plan_feature DROP FOREIGN KEY FK_A1683D6E60E4B879');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE plan_feature');
        $this->addSql('ALTER TABLE plan ADD features LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}

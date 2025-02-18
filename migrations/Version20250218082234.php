<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218082234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan ADD features LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD price NUMERIC(10, 2) NOT NULL, DROP yearly, DROP monthly');
        $this->addSql('ALTER TABLE subscription DROP title, DROP amount, DROP periodicity, DROP start_at, DROP end_at, DROP features');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan ADD monthly NUMERIC(10, 2) NOT NULL, DROP features, CHANGE price yearly NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD title VARCHAR(255) NOT NULL, ADD amount NUMERIC(10, 2) NOT NULL, ADD periodicity INT NOT NULL, ADD start_at DATE NOT NULL, ADD end_at DATE DEFAULT NULL, ADD features LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}

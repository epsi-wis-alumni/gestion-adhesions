<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128155345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_newsletter (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id INT NOT NULL, newsletter_id INT NOT NULL, opened_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D9E24324A76ED395 (user_id), INDEX IDX_D9E2432422DB1917 (newsletter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_newsletter ADD CONSTRAINT FK_D9E24324A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_newsletter ADD CONSTRAINT FK_D9E2432422DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_newsletter DROP FOREIGN KEY FK_D9E24324A76ED395');
        $this->addSql('ALTER TABLE user_newsletter DROP FOREIGN KEY FK_D9E2432422DB1917');
        $this->addSql('DROP TABLE user_newsletter');
    }
}

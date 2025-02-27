<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227151010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE election ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE election ADD CONSTRAINT FK_DCA03800896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_DCA03800896DBBDE ON election (updated_by_id)');
        $this->addSql('ALTER TABLE event CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE newsletter ADD updated_by_id INT DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_by_id created_by_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C8896DBBDE FOREIGN KEY (updated_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_7E8585C8896DBBDE ON newsletter (updated_by_id)');
        $this->addSql('ALTER TABLE transaction ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE election DROP FOREIGN KEY FK_DCA03800896DBBDE');
        $this->addSql('DROP INDEX IDX_DCA03800896DBBDE ON election');
        $this->addSql('ALTER TABLE election DROP updated_by_id, DROP updated_at, CHANGE created_by_id created_by_id INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE event CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C8896DBBDE');
        $this->addSql('DROP INDEX IDX_7E8585C8896DBBDE ON newsletter');
        $this->addSql('ALTER TABLE newsletter DROP updated_by_id, DROP updated_at, CHANGE created_by_id created_by_id INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE transaction DROP updated_at, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE `user` DROP updated_at, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}

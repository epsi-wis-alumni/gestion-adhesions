<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114150831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64992E67029');
        $this->addSql('ALTER TABLE membership DROP FOREIGN KEY FK_86FFD2859B6B5FBA');
        $this->addSql('DROP TABLE membership');
        $this->addSql('DROP INDEX UNIQ_8D93D64992E67029 ON user');
        $this->addSql('ALTER TABLE user ADD rejected_by_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD approved_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD rejected_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE approuved_by_id approved_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492D234F6A FOREIGN KEY (approved_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649CBF05FC9 FOREIGN KEY (rejected_by_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6492D234F6A ON user (approved_by_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649CBF05FC9 ON user (rejected_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE membership (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, firstname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lastname VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, company VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, job_title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', approuved_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status INT NOT NULL, UNIQUE INDEX UNIQ_86FFD2859B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE membership ADD CONSTRAINT FK_86FFD2859B6B5FBA FOREIGN KEY (account_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492D234F6A');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649CBF05FC9');
        $this->addSql('DROP INDEX IDX_8D93D6492D234F6A ON `user`');
        $this->addSql('DROP INDEX IDX_8D93D649CBF05FC9 ON `user`');
        $this->addSql('ALTER TABLE `user` ADD approuved_by_id INT DEFAULT NULL, DROP approved_by_id, DROP rejected_by_id, DROP created_at, DROP approved_at, DROP rejected_at');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64992E67029 FOREIGN KEY (approuved_by_id) REFERENCES membership (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64992E67029 ON `user` (approuved_by_id)');
    }
}

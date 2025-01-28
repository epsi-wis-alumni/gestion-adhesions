<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124143339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mail_template DROP FOREIGN KEY FK_4AB7DECB22DB1917');
        $this->addSql('DROP INDEX IDX_4AB7DECB22DB1917 ON mail_template');
        $this->addSql('ALTER TABLE mail_template DROP newsletter_id');
        $this->addSql('ALTER TABLE newsletter ADD template_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE newsletter ADD CONSTRAINT FK_7E8585C85DA0FB8 FOREIGN KEY (template_id) REFERENCES mail_template (id)');
        $this->addSql('CREATE INDEX IDX_7E8585C85DA0FB8 ON newsletter (template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mail_template ADD newsletter_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE mail_template ADD CONSTRAINT FK_4AB7DECB22DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id)');
        $this->addSql('CREATE INDEX IDX_4AB7DECB22DB1917 ON mail_template (newsletter_id)');
        $this->addSql('ALTER TABLE newsletter DROP FOREIGN KEY FK_7E8585C85DA0FB8');
        $this->addSql('DROP INDEX IDX_7E8585C85DA0FB8 ON newsletter');
        $this->addSql('ALTER TABLE newsletter DROP template_id');
    }
}

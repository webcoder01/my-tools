<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240121200856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget_category ADD user_id VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE budget_category ADD CONSTRAINT FK_D1834865A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_D1834865A76ED395 ON budget_category (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget_category DROP FOREIGN KEY FK_D1834865A76ED395');
        $this->addSql('DROP INDEX IDX_D1834865A76ED395 ON budget_category');
        $this->addSql('ALTER TABLE budget_category DROP user_id');
    }
}

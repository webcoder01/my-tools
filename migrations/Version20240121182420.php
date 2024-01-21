<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240121182420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget (id VARCHAR(36) NOT NULL, type_id VARCHAR(36) DEFAULT NULL, assigned_amount NUMERIC(7, 2) NOT NULL, available_amount NUMERIC(7, 2) NOT NULL, month SMALLINT NOT NULL, year SMALLINT NOT NULL, INDEX IDX_73F2F77BC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE budget_category (id VARCHAR(36) NOT NULL, name VARCHAR(80) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE budget_type (id VARCHAR(36) NOT NULL, category_id VARCHAR(36) DEFAULT NULL, name VARCHAR(80) NOT NULL, INDEX IDX_F61A557912469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE budget ADD CONSTRAINT FK_73F2F77BC54C8C93 FOREIGN KEY (type_id) REFERENCES budget_type (id)');
        $this->addSql('ALTER TABLE budget_type ADD CONSTRAINT FK_F61A557912469DE2 FOREIGN KEY (category_id) REFERENCES budget_category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget DROP FOREIGN KEY FK_73F2F77BC54C8C93');
        $this->addSql('ALTER TABLE budget_type DROP FOREIGN KEY FK_F61A557912469DE2');
        $this->addSql('DROP TABLE budget');
        $this->addSql('DROP TABLE budget_category');
        $this->addSql('DROP TABLE budget_type');
    }
}

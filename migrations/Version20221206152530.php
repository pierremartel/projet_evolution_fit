<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206152530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE status status TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE newsletter newsletter TINYINT(1) NULL, CHANGE created_at created_at DATETIME NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE newsletter newsletter TINYINT(1) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }
}

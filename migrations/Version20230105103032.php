<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230105103032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_shipping (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(4, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase ADD purchase_shipping_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B333AEB01 FOREIGN KEY (purchase_shipping_id) REFERENCES purchase_shipping (id)');
        $this->addSql('CREATE INDEX IDX_6117D13B333AEB01 ON purchase (purchase_shipping_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B333AEB01');
        $this->addSql('DROP TABLE purchase_shipping');
        $this->addSql('DROP INDEX IDX_6117D13B333AEB01 ON purchase');
        $this->addSql('ALTER TABLE purchase DROP purchase_shipping_id');
    }
}

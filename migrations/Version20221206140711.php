<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221206140711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_attr ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_attr ADD CONSTRAINT FK_899601B54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_899601B54584665A ON product_attr (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_attr DROP FOREIGN KEY FK_899601B54584665A');
        $this->addSql('DROP INDEX IDX_899601B54584665A ON product_attr');
        $this->addSql('ALTER TABLE product_attr DROP product_id');
       
    }
}

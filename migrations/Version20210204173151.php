<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204173151 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F3243BB18');
        $this->addSql('DROP INDEX IDX_C53D045F3243BB18 ON image');
        $this->addSql('ALTER TABLE image CHANGE hotel_id hotel_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F9C905093 FOREIGN KEY (hotel_id_id) REFERENCES hotel (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F9C905093 ON image (hotel_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F9C905093');
        $this->addSql('DROP INDEX IDX_C53D045F9C905093 ON image');
        $this->addSql('ALTER TABLE image CHANGE hotel_id_id hotel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F3243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('CREATE INDEX IDX_C53D045F3243BB18 ON image (hotel_id)');
    }
}

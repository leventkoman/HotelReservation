<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210215093327 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rezervation (id INT AUTO_INCREMENT NOT NULL, usreid INT DEFAULT NULL, hotelid INT DEFAULT NULL, roomid INT DEFAULT NULL, name VARCHAR(20) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, checkin DATETIME DEFAULT NULL, checkout DATETIME DEFAULT NULL, days INT DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, ip VARCHAR(20) DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, note VARCHAR(255) DEFAULT NULL, status VARCHAR(10) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE rezervation');
    }
}

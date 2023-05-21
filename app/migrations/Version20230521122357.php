<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521122357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL, ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD unique_id_code VARCHAR(255) NOT NULL, ADD age INT NOT NULL, ADD behaviour VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD is_insured TINYINT(1) DEFAULT NULL, ADD citizenship VARCHAR(255) NOT NULL, ADD exam_room INT DEFAULT NULL, ADD leave_max INT DEFAULT NULL, ADD schedule LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP username, DROP first_name, DROP last_name, DROP unique_id_code, DROP age, DROP behaviour, DROP created_at, DROP is_insured, DROP citizenship, DROP exam_room, DROP leave_max, DROP schedule');
    }
}

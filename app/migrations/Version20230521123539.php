<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230521123539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `leave` (id INT AUTO_INCREMENT NOT NULL, practitioner_id INT NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, INDEX IDX_9BB080D01121EA2C (practitioner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timekeeping (id INT AUTO_INCREMENT NOT NULL, practitioner_id INT NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, INDEX IDX_B962EB211121EA2C (practitioner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `leave` ADD CONSTRAINT FK_9BB080D01121EA2C FOREIGN KEY (practitioner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE timekeeping ADD CONSTRAINT FK_B962EB211121EA2C FOREIGN KEY (practitioner_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `leave` DROP FOREIGN KEY FK_9BB080D01121EA2C');
        $this->addSql('ALTER TABLE timekeeping DROP FOREIGN KEY FK_B962EB211121EA2C');
        $this->addSql('DROP TABLE `leave`');
        $this->addSql('DROP TABLE timekeeping');
    }
}

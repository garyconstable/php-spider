<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190401190749 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE email (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE domains CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE domain domain VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE queue CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE url url VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX url ON external_domain');
        $this->addSql('ALTER TABLE pending CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE url url VARCHAR(255) NOT NULL, CHANGE filename filename VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE process CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE parent_id parent_id INT NOT NULL, CHANGE worker_key worker_key VARCHAR(255) NOT NULL, CHANGE worker_type worker_type VARCHAR(255) NOT NULL, CHANGE worker_url worker_url VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE email');
        $this->addSql('ALTER TABLE domains CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE domain domain VARCHAR(255) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('CREATE UNIQUE INDEX url ON external_domain (url)');
        $this->addSql('ALTER TABLE pending CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE url url VARCHAR(255) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, CHANGE filename filename VARCHAR(255) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE process CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE parent_id parent_id INT DEFAULT 0, CHANGE worker_key worker_key VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, CHANGE worker_type worker_type VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, CHANGE worker_url worker_url VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE queue CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE url url VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci');
    }
}

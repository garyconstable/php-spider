<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190320230910 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE process (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, pid INTEGER NOT NULL, date_add DATETIME NOT NULL)');
        $this->addSql('DROP INDEX domains_domain_uindex');
        $this->addSql('CREATE TEMPORARY TABLE __temp__domains AS SELECT id, domain FROM domains');
        $this->addSql('DROP TABLE domains');
        $this->addSql('CREATE TABLE domains (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, domain VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO domains (id, domain) SELECT id, domain FROM __temp__domains');
        $this->addSql('DROP TABLE __temp__domains');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE process');
        $this->addSql('CREATE TEMPORARY TABLE __temp__domains AS SELECT id, domain FROM domains');
        $this->addSql('DROP TABLE domains');
        $this->addSql('CREATE TABLE domains (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, domain VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO domains (id, domain) SELECT id, domain FROM __temp__domains');
        $this->addSql('DROP TABLE __temp__domains');
        $this->addSql('CREATE UNIQUE INDEX domains_domain_uindex ON domains (domain)');
    }
}

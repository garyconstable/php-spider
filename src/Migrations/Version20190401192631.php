<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190401192631 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // $this->addSql('ALTER TABLE `domains` ADD UNIQUE (`domain`); ');

        $this->addSql('ALTER TABLE `email` ADD UNIQUE (`email`); ');

        $this->addSql('ALTER TABLE `external_domain` ADD UNIQUE (`url`); ');

        $this->addSql('ALTER TABLE `pending` ADD UNIQUE (`url`); ');

        // $this->addSql('ALTER TABLE `queue` ADD UNIQUE (`url`); ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}

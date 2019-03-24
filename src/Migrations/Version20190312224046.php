<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190312224046 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $queue = "CREATE TABLE `queue` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `url` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;)";
        $this->addSql($queue);


        $pending= "CREATE TABLE `pending` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `url` varchar(255) NOT NULL DEFAULT '',
        `filename` varchar(255) NOT NULL DEFAULT '',
        `date_add` datetime NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->addSql($pending);


        $domain = "CREATE TABLE `domains` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `domain` varchar(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->addSql($domain);


        $process = "CREATE TABLE `process` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `pid` int(11) NOT NULL,
        `date_add` datetime NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->addSql($process);

    }

    public function down(Schema $schema) : void{}
}

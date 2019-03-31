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
        ) ENGINE=InnoDB AUTO_INCREMENT=6673338 DEFAULT CHARSET=utf8;";
        $this->addSql($queue);


        $pending= "CREATE TABLE `pending` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `url` varchar(255) NOT NULL DEFAULT '',
        `filename` varchar(255) NOT NULL DEFAULT '',
        `date_add` datetime NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=10209 DEFAULT CHARSET=utf8;";
        $this->addSql($pending);


        $domain = "CREATE TABLE `domains` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `domain` varchar(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=434540 DEFAULT CHARSET=utf8;";
        $this->addSql($domain);


        $process = "CREATE TABLE `process` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `parent_id` int(11) DEFAULT '0',
        `pid` int(11) NOT NULL,
        `worker_key` varchar(255) DEFAULT NULL,
        `worker_type` varchar(255) DEFAULT NULL,
        `worker_url` varchar(255) DEFAULT NULL,
        `date_add` datetime NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=23667 DEFAULT CHARSET=utf8;";
        $this->addSql($process);

        $ext = "CREATE TABLE `external_domain` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `url` varchar(255) NOT NULL,
        `visited` tinyint(1) NOT NULL,
        `date_add` datetime NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `url` (`url`)
        ) ENGINE=InnoDB AUTO_INCREMENT=42672 DEFAULT CHARSET=utf8;";
        $this->addSql($process);

    }

    public function down(Schema $schema) : void{}
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190719214853 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE domain_name_domain_suffix (domain_name_id INT NOT NULL, domain_suffix_id INT NOT NULL, INDEX IDX_F6298ED193C085CE (domain_name_id), INDEX IDX_F6298ED1B885B2F1 (domain_suffix_id), PRIMARY KEY(domain_name_id, domain_suffix_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE domain_name_domain_suffix ADD CONSTRAINT FK_F6298ED193C085CE FOREIGN KEY (domain_name_id) REFERENCES domain_name (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE domain_name_domain_suffix ADD CONSTRAINT FK_F6298ED1B885B2F1 FOREIGN KEY (domain_suffix_id) REFERENCES domain_suffix (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE domain_name_domain_suffix');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190719214639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE domain_name (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE domain_name_domain_prefix (domain_name_id INT NOT NULL, domain_prefix_id INT NOT NULL, INDEX IDX_D0288F8193C085CE (domain_name_id), INDEX IDX_D0288F81BA259DB0 (domain_prefix_id), PRIMARY KEY(domain_name_id, domain_prefix_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE domain_name_domain_prefix ADD CONSTRAINT FK_D0288F8193C085CE FOREIGN KEY (domain_name_id) REFERENCES domain_name (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE domain_name_domain_prefix ADD CONSTRAINT FK_D0288F81BA259DB0 FOREIGN KEY (domain_prefix_id) REFERENCES domain_prefix (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE domain_name_domain_prefix DROP FOREIGN KEY FK_D0288F8193C085CE');
        $this->addSql('DROP TABLE domain_name');
        $this->addSql('DROP TABLE domain_name_domain_prefix');
        $this->addSql('ALTER TABLE cron_report CHANGE job_id job_id INT DEFAULT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200504095409 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition_load_date (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, status INT DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE industry_user (industry_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_17662E7A2B19A734 (industry_id), INDEX IDX_17662E7AA76ED395 (user_id), PRIMARY KEY(industry_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, last_notify_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE industry_user ADD CONSTRAINT FK_17662E7A2B19A734 FOREIGN KEY (industry_id) REFERENCES industry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE industry_user ADD CONSTRAINT FK_17662E7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition ADD competition_load_date_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1B463CF15 FOREIGN KEY (competition_load_date_id) REFERENCES competition_load_date (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B50A2CB1B463CF15 ON competition (competition_load_date_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1B463CF15');
        $this->addSql('ALTER TABLE industry_user DROP FOREIGN KEY FK_17662E7AA76ED395');
        $this->addSql('DROP TABLE competition_load_date');
        $this->addSql('DROP TABLE industry_user');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX UNIQ_B50A2CB1B463CF15 ON competition');
        $this->addSql('ALTER TABLE competition DROP competition_load_date_id');
    }
}

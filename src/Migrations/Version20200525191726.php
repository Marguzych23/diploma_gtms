<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200525191726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_industry (user_id INT NOT NULL, industry_id INT NOT NULL, INDEX IDX_2D7788A0A76ED395 (user_id), INDEX IDX_2D7788A02B19A734 (industry_id), PRIMARY KEY(user_id, industry_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_industry ADD CONSTRAINT FK_2D7788A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_industry ADD CONSTRAINT FK_2D7788A02B19A734 FOREIGN KEY (industry_id) REFERENCES industry (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE industry_user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE industry_user (industry_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_17662E7AA76ED395 (user_id), INDEX IDX_17662E7A2B19A734 (industry_id), PRIMARY KEY(industry_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE industry_user ADD CONSTRAINT FK_17662E7A2B19A734 FOREIGN KEY (industry_id) REFERENCES industry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE industry_user ADD CONSTRAINT FK_17662E7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user_industry');
    }
}

<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200502120606 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, deadline DATETIME DEFAULT NULL, grant_size VARCHAR(512) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, update_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE industry (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE industry_competition (industry_id INT NOT NULL, competition_id INT NOT NULL, INDEX IDX_8769D6E72B19A734 (industry_id), INDEX IDX_8769D6E77B39D312 (competition_id), PRIMARY KEY(industry_id, competition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE industry_competition ADD CONSTRAINT FK_8769D6E72B19A734 FOREIGN KEY (industry_id) REFERENCES industry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE industry_competition ADD CONSTRAINT FK_8769D6E77B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');

        $this->addSql("INSERT INTO industry (id, name) VALUES
                                (1,      'Математика'),
                                (2,      'Информационные технологии и вычислительные системы'),
                                (3,      'Физика и астрономия'),
                                (4,      'Химия и науки о материалах'),
                                (5,      'Биология'),
                                (6,      'Медицина'),
                                (7,      'Науки о Земле'),
                                (8,      'Лингвистика и культурология'),
                                (9,      'История, археология, этнология, антропология'),
                                (10,     'Философия, политология, социология, правоведение, история науки и техники, науковедение'),
                                (11,     'Психология, фундаментальные проблемы образования, социальные проблемы здоровья и экологии человека'),
                                (12,     'Глобальные проблемы и международные отношения'),
                                (13,     'Инженерные науки'),
                                (14,     'Сельскохозяйственные науки'),
                                (15,     'Экономика');"
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE industry_competition DROP FOREIGN KEY FK_8769D6E77B39D312');
        $this->addSql('ALTER TABLE industry_competition DROP FOREIGN KEY FK_8769D6E72B19A734');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE industry');
        $this->addSql('DROP TABLE industry_competition');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230311144526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_grade (id INT AUTO_INCREMENT NOT NULL, shortname VARCHAR(50) NOT NULL, longname VARCHAR(250) DEFAULT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_race (id INT AUTO_INCREMENT NOT NULL, importfilename VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_ranking (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, run_id INT DEFAULT NULL, end DATETIME DEFAULT NULL, INDEX IDX_DBEB31A4CB944F1A (student_id), INDEX IDX_DBEB31A484E3FEC4 (run_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_run (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_student (id INT AUTO_INCREMENT NOT NULL, grade_id INT NOT NULL, lastname VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, gender VARCHAR(1) NOT NULL, mas DOUBLE PRECISION DEFAULT NULL, objective TIME DEFAULT NULL, INDEX IDX_EC70A747FE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_ranking ADD CONSTRAINT FK_DBEB31A4CB944F1A FOREIGN KEY (student_id) REFERENCES tbl_student (id)');
        $this->addSql('ALTER TABLE tbl_ranking ADD CONSTRAINT FK_DBEB31A484E3FEC4 FOREIGN KEY (run_id) REFERENCES tbl_run (id)');
        $this->addSql('ALTER TABLE tbl_student ADD CONSTRAINT FK_EC70A747FE19A1A8 FOREIGN KEY (grade_id) REFERENCES tbl_grade (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_ranking DROP FOREIGN KEY FK_DBEB31A4CB944F1A');
        $this->addSql('ALTER TABLE tbl_ranking DROP FOREIGN KEY FK_DBEB31A484E3FEC4');
        $this->addSql('ALTER TABLE tbl_student DROP FOREIGN KEY FK_EC70A747FE19A1A8');
        $this->addSql('DROP TABLE tbl_grade');
        $this->addSql('DROP TABLE tbl_race');
        $this->addSql('DROP TABLE tbl_ranking');
        $this->addSql('DROP TABLE tbl_run');
        $this->addSql('DROP TABLE tbl_student');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

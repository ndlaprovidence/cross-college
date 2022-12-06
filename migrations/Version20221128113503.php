<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128113503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tbl_grade (id INT AUTO_INCREMENT NOT NULL, shortname VARCHAR(10) NOT NULL, longname VARCHAR(50) DEFAULT NULL, level INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_race (id INT AUTO_INCREMENT NOT NULL, start DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_ranking (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, student_id INT NOT NULL, end DATETIME DEFAULT NULL, INDEX IDX_DBEB31A46E59D40D (race_id), INDEX IDX_DBEB31A4CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tbl_student (id INT AUTO_INCREMENT NOT NULL, grade_id INT NOT NULL, lastname VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, gender VARCHAR(1) NOT NULL, mas DOUBLE PRECISION DEFAULT NULL, INDEX IDX_EC70A747FE19A1A8 (grade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tbl_ranking ADD CONSTRAINT FK_DBEB31A46E59D40D FOREIGN KEY (race_id) REFERENCES tbl_race (id)');
        $this->addSql('ALTER TABLE tbl_ranking ADD CONSTRAINT FK_DBEB31A4CB944F1A FOREIGN KEY (student_id) REFERENCES tbl_student (id)');
        $this->addSql('ALTER TABLE tbl_student ADD CONSTRAINT FK_EC70A747FE19A1A8 FOREIGN KEY (grade_id) REFERENCES tbl_grade (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tbl_ranking DROP FOREIGN KEY FK_DBEB31A46E59D40D');
        $this->addSql('ALTER TABLE tbl_ranking DROP FOREIGN KEY FK_DBEB31A4CB944F1A');
        $this->addSql('ALTER TABLE tbl_student DROP FOREIGN KEY FK_EC70A747FE19A1A8');
        $this->addSql('DROP TABLE tbl_grade');
        $this->addSql('DROP TABLE tbl_race');
        $this->addSql('DROP TABLE tbl_ranking');
        $this->addSql('DROP TABLE tbl_student');
    }
}

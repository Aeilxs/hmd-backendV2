<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230310230346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(20) NOT NULL, duration INT NOT NULL, intensity INT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_AC74095AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drug (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(50) NOT NULL, unit VARCHAR(50) NOT NULL, quantity INT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_43EB7A3EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, caloric_intake INT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D43829F7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hydration (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, quantity INT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C4D8DB3BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sleep (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, duration INT NOT NULL, quality INT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F33C2ACA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles VARCHAR(10) NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, gender VARCHAR(5) NOT NULL, size INT DEFAULT NULL, weight INT DEFAULT NULL, caloric_need INT DEFAULT NULL, date_of_birth DATE DEFAULT NULL, update_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE drug ADD CONSTRAINT FK_43EB7A3EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE food ADD CONSTRAINT FK_D43829F7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE hydration ADD CONSTRAINT FK_C4D8DB3BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sleep ADD CONSTRAINT FK_F33C2ACA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AA76ED395');
        $this->addSql('ALTER TABLE drug DROP FOREIGN KEY FK_43EB7A3EA76ED395');
        $this->addSql('ALTER TABLE food DROP FOREIGN KEY FK_D43829F7A76ED395');
        $this->addSql('ALTER TABLE hydration DROP FOREIGN KEY FK_C4D8DB3BA76ED395');
        $this->addSql('ALTER TABLE sleep DROP FOREIGN KEY FK_F33C2ACA76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE drug');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE hydration');
        $this->addSql('DROP TABLE sleep');
        $this->addSql('DROP TABLE `user`');
    }
}

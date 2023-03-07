<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230305100204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE estDans (id INT AUTO_INCREMENT NOT NULL, utilisateurs_id INT DEFAULT NULL, groupes_id INT DEFAULT NULL, INDEX IDX_AA178D8E1E969C5 (utilisateurs_id), INDEX IDX_AA178D8E305371B (groupes_id), UNIQUE INDEX utilisateur_groupe_unique (utilisateurs_id, groupes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupes (id INT AUTO_INCREMENT NOT NULL, createur_id INT DEFAULT NULL, nom_groupe VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_576366D973A201E5 (createur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupes_utilisateurs (groupes_id INT NOT NULL, utilisateurs_id INT NOT NULL, INDEX IDX_68C4EF99305371B (groupes_id), INDEX IDX_68C4EF991E969C5 (utilisateurs_id), PRIMARY KEY(groupes_id, utilisateurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) DEFAULT \'inconnu\', prenom VARCHAR(255) DEFAULT \'inconnu\', num_tel VARCHAR(20) DEFAULT \'Unknown\', vehicule TINYINT(1) DEFAULT 0 NOT NULL, genre VARCHAR(10) DEFAULT NULL, autorisation_mail TINYINT(1) NOT NULL, fichier_photo LONGBLOB DEFAULT NULL, cumul_notes INT NOT NULL, nb_notes INT NOT NULL, compte_actif TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_497B315EE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE estDans ADD CONSTRAINT FK_AA178D8E1E969C5 FOREIGN KEY (utilisateurs_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE estDans ADD CONSTRAINT FK_AA178D8E305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id)');
        $this->addSql('ALTER TABLE groupes ADD CONSTRAINT FK_576366D973A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE groupes_utilisateurs ADD CONSTRAINT FK_68C4EF99305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE groupes_utilisateurs ADD CONSTRAINT FK_68C4EF991E969C5 FOREIGN KEY (utilisateurs_id) REFERENCES utilisateurs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE estDans DROP FOREIGN KEY FK_AA178D8E1E969C5');
        $this->addSql('ALTER TABLE estDans DROP FOREIGN KEY FK_AA178D8E305371B');
        $this->addSql('ALTER TABLE groupes DROP FOREIGN KEY FK_576366D973A201E5');
        $this->addSql('ALTER TABLE groupes_utilisateurs DROP FOREIGN KEY FK_68C4EF99305371B');
        $this->addSql('ALTER TABLE groupes_utilisateurs DROP FOREIGN KEY FK_68C4EF991E969C5');
        $this->addSql('DROP TABLE estDans');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('DROP TABLE groupes_utilisateurs');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

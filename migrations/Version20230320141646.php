<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230320141646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adopte (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT NOT NULL, trajet_id INT NOT NULL, INDEX IDX_5FE70AF1FB88E14F (utilisateur_id), INDEX IDX_5FE70AF1D12A823 (trajet_id), UNIQUE INDEX utilisateur_trajet_unique (utilisateur_id, trajet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE estDans (id INT AUTO_INCREMENT NOT NULL, utilisateurs_id INT DEFAULT NULL, groupes_id INT DEFAULT NULL, INDEX IDX_AA178D8E1E969C5 (utilisateurs_id), INDEX IDX_AA178D8E305371B (groupes_id), UNIQUE INDEX utilisateur_groupe_unique (utilisateurs_id, groupes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE est_accepte (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, trajet_id INT DEFAULT NULL, INDEX IDX_2582B7C2FB88E14F (utilisateur_id), INDEX IDX_2582B7C2D12A823 (trajet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupes (id INT AUTO_INCREMENT NOT NULL, createur_id INT DEFAULT NULL, trajets_id INT DEFAULT NULL, nom_groupe VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_576366D973A201E5 (createur_id), INDEX IDX_576366D9451BDEFF (trajets_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, user_qui_demande_trajet_id INT DEFAULT NULL, trajet_qui_est_demande_id INT DEFAULT NULL, message VARCHAR(300) NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, type_notif INT DEFAULT 1 NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), INDEX IDX_BF5476CAB410D633 (user_qui_demande_trajet_id), INDEX IDX_BF5476CA2C704E12 (trajet_qui_est_demande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajets (id INT AUTO_INCREMENT NOT NULL, publie_id INT NOT NULL, demarrea_id INT NOT NULL, arrivea_id INT NOT NULL, etat VARCHAR(50) NOT NULL, t_depart DATETIME NOT NULL, t_arrivee DATETIME DEFAULT NULL, prix DOUBLE PRECISION DEFAULT NULL, nb_passager_max INT NOT NULL, nb_passager_courant INT NOT NULL, public TINYINT(1) NOT NULL, renseignement LONGTEXT DEFAULT NULL, INDEX IDX_FF2B5BA990DFD44D (publie_id), INDEX IDX_FF2B5BA97B7C4405 (demarrea_id), INDEX IDX_FF2B5BA96592E915 (arrivea_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) DEFAULT \'inconnu\', prenom VARCHAR(255) DEFAULT \'inconnu\', num_tel VARCHAR(20) DEFAULT \'Unknown\', vehicule TINYINT(1) DEFAULT 0 NOT NULL, genre VARCHAR(10) DEFAULT NULL, autorisation_mail TINYINT(1) NOT NULL, fichier_photo VARCHAR(255) DEFAULT NULL, cumul_notes INT NOT NULL, nb_notes INT NOT NULL, compte_actif TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_497B315EE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE villes (id INT AUTO_INCREMENT NOT NULL, nom_ville VARCHAR(100) NOT NULL, cp INT DEFAULT NULL, UNIQUE INDEX ville_unique (nom_ville, CP), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adopte ADD CONSTRAINT FK_5FE70AF1FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE adopte ADD CONSTRAINT FK_5FE70AF1D12A823 FOREIGN KEY (trajet_id) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE estDans ADD CONSTRAINT FK_AA178D8E1E969C5 FOREIGN KEY (utilisateurs_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE estDans ADD CONSTRAINT FK_AA178D8E305371B FOREIGN KEY (groupes_id) REFERENCES groupes (id)');
        $this->addSql('ALTER TABLE est_accepte ADD CONSTRAINT FK_2582B7C2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE est_accepte ADD CONSTRAINT FK_2582B7C2D12A823 FOREIGN KEY (trajet_id) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE groupes ADD CONSTRAINT FK_576366D973A201E5 FOREIGN KEY (createur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE groupes ADD CONSTRAINT FK_576366D9451BDEFF FOREIGN KEY (trajets_id) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAB410D633 FOREIGN KEY (user_qui_demande_trajet_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA2C704E12 FOREIGN KEY (trajet_qui_est_demande_id) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE trajets ADD CONSTRAINT FK_FF2B5BA990DFD44D FOREIGN KEY (publie_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE trajets ADD CONSTRAINT FK_FF2B5BA97B7C4405 FOREIGN KEY (demarrea_id) REFERENCES villes (id)');
        $this->addSql('ALTER TABLE trajets ADD CONSTRAINT FK_FF2B5BA96592E915 FOREIGN KEY (arrivea_id) REFERENCES villes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adopte DROP FOREIGN KEY FK_5FE70AF1FB88E14F');
        $this->addSql('ALTER TABLE adopte DROP FOREIGN KEY FK_5FE70AF1D12A823');
        $this->addSql('ALTER TABLE estDans DROP FOREIGN KEY FK_AA178D8E1E969C5');
        $this->addSql('ALTER TABLE estDans DROP FOREIGN KEY FK_AA178D8E305371B');
        $this->addSql('ALTER TABLE est_accepte DROP FOREIGN KEY FK_2582B7C2FB88E14F');
        $this->addSql('ALTER TABLE est_accepte DROP FOREIGN KEY FK_2582B7C2D12A823');
        $this->addSql('ALTER TABLE groupes DROP FOREIGN KEY FK_576366D973A201E5');
        $this->addSql('ALTER TABLE groupes DROP FOREIGN KEY FK_576366D9451BDEFF');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAB410D633');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA2C704E12');
        $this->addSql('ALTER TABLE trajets DROP FOREIGN KEY FK_FF2B5BA990DFD44D');
        $this->addSql('ALTER TABLE trajets DROP FOREIGN KEY FK_FF2B5BA97B7C4405');
        $this->addSql('ALTER TABLE trajets DROP FOREIGN KEY FK_FF2B5BA96592E915');
        $this->addSql('DROP TABLE adopte');
        $this->addSql('DROP TABLE estDans');
        $this->addSql('DROP TABLE est_accepte');
        $this->addSql('DROP TABLE groupes');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE trajets');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE villes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

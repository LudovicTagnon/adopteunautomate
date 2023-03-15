<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315110224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE est_accepte (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, trajet_id INT DEFAULT NULL, INDEX IDX_2582B7C2FB88E14F (utilisateur_id), INDEX IDX_2582B7C2D12A823 (trajet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE est_accepte ADD CONSTRAINT FK_2582B7C2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE est_accepte ADD CONSTRAINT FK_2582B7C2D12A823 FOREIGN KEY (trajet_id) REFERENCES trajets (id)');
        $this->addSql('ALTER TABLE trajets CHANGE t_arrivee t_arrivee DATETIME DEFAULT NULL, CHANGE prix prix DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateurs CHANGE nom nom VARCHAR(50) DEFAULT \'inconnu\', CHANGE prenom prenom VARCHAR(255) DEFAULT \'inconnu\', CHANGE num_tel num_tel VARCHAR(20) DEFAULT \'Unknown\', CHANGE genre genre VARCHAR(10) DEFAULT NULL, CHANGE fichier_photo fichier_photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE est_accepte DROP FOREIGN KEY FK_2582B7C2FB88E14F');
        $this->addSql('ALTER TABLE est_accepte DROP FOREIGN KEY FK_2582B7C2D12A823');
        $this->addSql('DROP TABLE est_accepte');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE trajets CHANGE t_arrivee t_arrivee DATETIME DEFAULT \'NULL\', CHANGE prix prix DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE utilisateurs CHANGE nom nom VARCHAR(50) DEFAULT \'\'\'inconnu\'\'\', CHANGE prenom prenom VARCHAR(255) DEFAULT \'\'\'inconnu\'\'\', CHANGE num_tel num_tel VARCHAR(20) DEFAULT \'\'\'Unknown\'\'\', CHANGE genre genre VARCHAR(10) DEFAULT \'NULL\', CHANGE fichier_photo fichier_photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}

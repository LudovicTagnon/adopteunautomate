<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315141905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trajets CHANGE t_arrivee t_arrivee DATETIME DEFAULT NULL, CHANGE prix prix DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateurs CHANGE nom nom VARCHAR(50) DEFAULT \'inconnu\', CHANGE prenom prenom VARCHAR(255) DEFAULT \'inconnu\', CHANGE num_tel num_tel VARCHAR(20) DEFAULT \'Unknown\', CHANGE genre genre VARCHAR(10) DEFAULT NULL, CHANGE fichier_photo fichier_photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE trajets CHANGE t_arrivee t_arrivee DATETIME DEFAULT \'NULL\', CHANGE prix prix DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE utilisateurs CHANGE nom nom VARCHAR(50) DEFAULT \'\'\'inconnu\'\'\', CHANGE prenom prenom VARCHAR(255) DEFAULT \'\'\'inconnu\'\'\', CHANGE num_tel num_tel VARCHAR(20) DEFAULT \'\'\'Unknown\'\'\', CHANGE genre genre VARCHAR(10) DEFAULT \'NULL\', CHANGE fichier_photo fichier_photo VARCHAR(255) DEFAULT \'NULL\'');
    }
}

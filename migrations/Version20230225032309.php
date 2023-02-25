<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230225124538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateurs ADD nom VARCHAR(50) DEFAULT \'Unknown\' NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD prenom VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD num_tel VARCHAR(20) DEFAULT \'Unknown\' NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD vehicule TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD genre ENUM(\'homme\', \'femme\', \'autre\') DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD autorisation_mail TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD fichier_photo LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD cumul_notes INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD nb_notes INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE utilisateurs ADD compte_actif TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateurs DROP nom');
        $this->addSql('ALTER TABLE utilisateurs DROP prenom');
        $this->addSql('ALTER TABLE utilisateurs DROP num_tel');
        $this->addSql('ALTER TABLE utilisateurs DROP vehicule');
        $this->addSql('ALTER TABLE utilisateurs DROP genre');
        $this->addSql('ALTER TABLE utilisateurs DROP autorisation_mail');
        $this->addSql('ALTER TABLE utilisateurs DROP fichier_photo');
        $this->addSql('ALTER TABLE utilisateurs DROP cumul_notes');
        $this->addSql('ALTER TABLE utilisateurs DROP nb_notes');
        $this->addSql('ALTER TABLE utilisateurs DROP compte_actif');
    }
}

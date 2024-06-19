<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240523033048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Vérifiez si la colonne 'username' existe avant de la supprimer
        $this->skipIf(!$schema->getTable('admin')->hasColumn('username'), 'Column username does not exist in admin table, skipping drop.');
        $this->addSql('ALTER TABLE admin DROP COLUMN username');
        
        // Vérifiez si la colonne 'roles' existe avant de la supprimer
        $this->skipIf(!$schema->getTable('admin')->hasColumn('roles'), 'Column roles does not exist in admin table, skipping drop.');
        $this->addSql('ALTER TABLE admin DROP COLUMN roles');

        // Ajouter la colonne 'photo' à la table 'event'
        $this->addSql('ALTER TABLE event ADD photo VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Ajouter la colonne 'username' à la table 'admin' si elle n'existe pas
        $this->skipIf($schema->getTable('admin')->hasColumn('username'), 'Column username already exists in admin table, skipping add.');
        $this->addSql('ALTER TABLE admin ADD username VARCHAR(255) NOT NULL');
        
        // Ajouter la colonne 'roles' à la table 'admin' si elle n'existe pas
        $this->skipIf($schema->getTable('admin')->hasColumn('roles'), 'Column roles already exists in admin table, skipping add.');
        $this->addSql('ALTER TABLE admin ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');

        // Supprimer la colonne 'photo' de la table 'event'
        $this->addSql('ALTER TABLE event DROP COLUMN photo');
    }
}
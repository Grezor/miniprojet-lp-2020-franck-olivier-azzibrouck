<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210102021850 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE choixformule DROP FOREIGN KEY FK_9CA7AE2B2A68F4D1');
        $this->addSql('ALTER TABLE choixformule DROP FOREIGN KEY FK_9CA7AE2BA76ED395');
        $this->addSql('ALTER TABLE choixformule ADD CONSTRAINT FK_9CA7AE2B2A68F4D1 FOREIGN KEY (formule_id) REFERENCES formule (id)');
        $this->addSql('ALTER TABLE choixformule ADD CONSTRAINT FK_9CA7AE2BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E037A76ED395');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E037C4968C81');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E037A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E037C4968C81 FOREIGN KEY (id_dossier_id) REFERENCES dossier (id)');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F611C0C56');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE choixformule DROP FOREIGN KEY FK_9CA7AE2BA76ED395');
        $this->addSql('ALTER TABLE choixformule DROP FOREIGN KEY FK_9CA7AE2B2A68F4D1');
        $this->addSql('ALTER TABLE choixformule ADD CONSTRAINT FK_9CA7AE2BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE choixformule ADD CONSTRAINT FK_9CA7AE2B2A68F4D1 FOREIGN KEY (formule_id) REFERENCES formule (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E037C4968C81');
        $this->addSql('ALTER TABLE dossier DROP FOREIGN KEY FK_3D48E037A76ED395');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E037C4968C81 FOREIGN KEY (id_dossier_id) REFERENCES dossier (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dossier ADD CONSTRAINT FK_3D48E037A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551F611C0C56');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210128203527 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C74642B8210');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C74A76ED395');
        $this->addSql('ALTER TABLE email ADD statut TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C74642B8210');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C74A76ED395');
        $this->addSql('ALTER TABLE email DROP statut');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74642B8210 FOREIGN KEY (admin_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C74A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}

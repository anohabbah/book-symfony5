<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201103172416 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments ALTER photo_filename DROP NOT NULL');
        $this->addSql('ALTER TABLE comments ALTER state SET DEFAULT \'submitted\'');
        $this->addSql('ALTER TABLE comments ALTER state TYPE VARCHAR(20)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comments ALTER photo_filename SET NOT NULL');
        $this->addSql('ALTER TABLE comments ALTER state DROP DEFAULT');
        $this->addSql('ALTER TABLE comments ALTER state TYPE VARCHAR(255)');
    }
}

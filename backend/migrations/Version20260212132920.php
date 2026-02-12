<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260212132920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE championnat (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, sport_id INT NOT NULL, INDEX IDX_AB8C220AC78BCF8 (sport_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, championnat_id INT NOT NULL, INDEX IDX_B50A2CB1627A0DA8 (championnat_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE epreuve (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, competition_id INT NOT NULL, INDEX IDX_D6ADE47F7B39D312 (competition_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sport (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE championnat ADD CONSTRAINT FK_AB8C220AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sport (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1627A0DA8 FOREIGN KEY (championnat_id) REFERENCES championnat (id)');
        $this->addSql('ALTER TABLE epreuve ADD CONSTRAINT FK_D6ADE47F7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE championnat DROP FOREIGN KEY FK_AB8C220AC78BCF8');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1627A0DA8');
        $this->addSql('ALTER TABLE epreuve DROP FOREIGN KEY FK_D6ADE47F7B39D312');
        $this->addSql('DROP TABLE championnat');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE epreuve');
        $this->addSql('DROP TABLE sport');
    }
}

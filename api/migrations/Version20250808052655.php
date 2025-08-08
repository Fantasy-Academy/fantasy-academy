<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808052655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE challenge (id UUID NOT NULL, name TEXT NOT NULL, short_description TEXT NOT NULL, description TEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, starts_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, max_points INT NOT NULL, hint_text TEXT DEFAULT NULL, hint_image VARCHAR(255) DEFAULT NULL, skill_analytical INT NOT NULL, skill_strategic_planning INT NOT NULL, skill_adaptability INT NOT NULL, skill_premier_league_knowledge INT NOT NULL, skill_risk_management INT NOT NULL, skill_decision_making_under_pressure INT NOT NULL, skill_financial_management INT NOT NULL, skill_long_term_vision INT NOT NULL, skill_discipline INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE question (id UUID NOT NULL, text TEXT NOT NULL, type VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, numeric_constraint JSONB DEFAULT NULL, choice_constraint JSONB DEFAULT NULL, challenge_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_B6F7494E98A21AC6 ON question (challenge_id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question DROP CONSTRAINT FK_B6F7494E98A21AC6');
        $this->addSql('DROP TABLE challenge');
        $this->addSql('DROP TABLE question');
    }
}

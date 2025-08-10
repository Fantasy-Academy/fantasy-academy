<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250810220440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge ALTER skill_analytical TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_strategic_planning TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_adaptability TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_premier_league_knowledge TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_risk_management TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_decision_making_under_pressure TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_financial_management TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_long_term_vision TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE challenge ALTER skill_discipline TYPE DOUBLE PRECISION');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE challenge ALTER skill_analytical TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_strategic_planning TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_adaptability TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_premier_league_knowledge TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_risk_management TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_decision_making_under_pressure TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_financial_management TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_long_term_vision TYPE INT');
        $this->addSql('ALTER TABLE challenge ALTER skill_discipline TYPE INT');
    }
}

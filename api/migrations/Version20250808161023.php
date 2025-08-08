<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808161023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE player_answered_question (answered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text_answer VARCHAR(255) DEFAULT NULL, numeric_answer DOUBLE PRECISION DEFAULT NULL, selected_choice_id UUID DEFAULT NULL, selected_choice_ids JSONB DEFAULT NULL, ordered_choice_ids JSONB DEFAULT NULL, question_id UUID NOT NULL, challenge_answer_id UUID NOT NULL, PRIMARY KEY (question_id, challenge_answer_id))');
        $this->addSql('CREATE INDEX IDX_87CCD2141E27F6BF ON player_answered_question (question_id)');
        $this->addSql('CREATE INDEX IDX_87CCD214AA49498 ON player_answered_question (challenge_answer_id)');
        $this->addSql('CREATE TABLE player_challenge_answer (points INT DEFAULT NULL, answered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, challenge_id UUID NOT NULL, user_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C8B76B7F98A21AC6 ON player_challenge_answer (challenge_id)');
        $this->addSql('CREATE INDEX IDX_C8B76B7FA76ED395 ON player_challenge_answer (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8B76B7FA76ED39598A21AC6 ON player_challenge_answer (user_id, challenge_id)');
        $this->addSql('ALTER TABLE player_answered_question ADD CONSTRAINT FK_87CCD2141E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE player_answered_question ADD CONSTRAINT FK_87CCD214AA49498 FOREIGN KEY (challenge_answer_id) REFERENCES player_challenge_answer (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE player_challenge_answer ADD CONSTRAINT FK_C8B76B7F98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE player_challenge_answer ADD CONSTRAINT FK_C8B76B7FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE question ALTER challenge_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_answered_question DROP CONSTRAINT FK_87CCD2141E27F6BF');
        $this->addSql('ALTER TABLE player_answered_question DROP CONSTRAINT FK_87CCD214AA49498');
        $this->addSql('ALTER TABLE player_challenge_answer DROP CONSTRAINT FK_C8B76B7F98A21AC6');
        $this->addSql('ALTER TABLE player_challenge_answer DROP CONSTRAINT FK_C8B76B7FA76ED395');
        $this->addSql('DROP TABLE player_answered_question');
        $this->addSql('DROP TABLE player_challenge_answer');
        $this->addSql('ALTER TABLE question ALTER challenge_id DROP NOT NULL');
    }
}

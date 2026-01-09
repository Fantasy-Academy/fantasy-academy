<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109115302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add subscription table and stripe_customer_id to user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription (status VARCHAR(50) NOT NULL, current_period_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, current_period_end TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, canceled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, cancel_at_period_end BOOLEAN DEFAULT false NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, id UUID NOT NULL, stripe_subscription_id VARCHAR(255) NOT NULL, stripe_customer_id VARCHAR(255) NOT NULL, plan_id VARCHAR(100) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3C664D3B5DBB761 ON subscription (stripe_subscription_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3A76ED395 ON subscription (user_id)');
        $this->addSql('CREATE INDEX idx_subscription_stripe_subscription_id ON subscription (stripe_subscription_id)');
        $this->addSql('CREATE INDEX idx_subscription_stripe_customer_id ON subscription (stripe_customer_id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE "user" ADD stripe_customer_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3A76ED395');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('ALTER TABLE "user" DROP stripe_customer_id');
    }
}

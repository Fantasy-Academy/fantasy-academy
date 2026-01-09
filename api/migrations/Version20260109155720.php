<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109155720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX idx_subscription_stripe_subscription_id RENAME TO IDX_A3C664D3B5DBB761');
        $this->addSql('ALTER INDEX idx_subscription_stripe_customer_id RENAME TO IDX_A3C664D3708DC647');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX idx_a3c664d3b5dbb761 RENAME TO idx_subscription_stripe_subscription_id');
        $this->addSql('ALTER INDEX idx_a3c664d3708dc647 RENAME TO idx_subscription_stripe_customer_id');
    }
}

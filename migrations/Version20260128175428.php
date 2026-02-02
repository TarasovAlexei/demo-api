<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260128175428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscriptions (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))');
        $this->addSql('CREATE INDEX IDX_4778A013AD8644E ON subscriptions (user_source)');
        $this->addSql('CREATE INDEX IDX_4778A01233D34C1 ON subscriptions (user_target)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A013AD8644E FOREIGN KEY (user_source) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01233D34C1 FOREIGN KEY (user_target) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A013AD8644E');
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A01233D34C1');
        $this->addSql('DROP TABLE subscriptions');
    }
}

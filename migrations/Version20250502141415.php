<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250502141415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE api_token (id SERIAL NOT NULL, owned_by_id INT NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, token VARCHAR(68) NOT NULL, scopes JSON NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7BA2F5EB5E70BCD7 ON api_token (owned_by_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN api_token.expires_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB5E70BCD7 FOREIGN KEY (owned_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE api_token DROP CONSTRAINT FK_7BA2F5EB5E70BCD7
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE api_token
        SQL);
    }
}

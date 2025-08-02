<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250802134443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_post DROP CONSTRAINT fk_ba5ae01df675f31b');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE api_token_id_seq CASCADE');
        $this->addSql('ALTER TABLE api_token DROP CONSTRAINT fk_7ba2f5eb5e70bcd7');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP INDEX idx_ba5ae01df675f31b');
        $this->addSql('ALTER TABLE blog_post DROP author_id');
        $this->addSql('ALTER TABLE blog_post ALTER content TYPE TEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE api_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE api_token (id SERIAL NOT NULL, owned_by_id INT NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, token VARCHAR(68) NOT NULL, scopes JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7ba2f5eb5e70bcd7 ON api_token (owned_by_id)');
        $this->addSql('COMMENT ON COLUMN api_token.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_email ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT fk_7ba2f5eb5e70bcd7 FOREIGN KEY (owned_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE blog_post ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE blog_post ALTER content TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT fk_ba5ae01df675f31b FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ba5ae01df675f31b ON blog_post (author_id)');
    }
}

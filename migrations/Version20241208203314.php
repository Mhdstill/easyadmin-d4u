<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208203314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE operation_user (operation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A2E38BD844AC3583 (operation_id), INDEX IDX_A2E38BD8A76ED395 (user_id), PRIMARY KEY(operation_id, user_id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE operation_user ADD CONSTRAINT FK_A2E38BD844AC3583 FOREIGN KEY (operation_id) REFERENCES operation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operation_user ADD CONSTRAINT FK_A2E38BD8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation_user DROP FOREIGN KEY FK_A2E38BD844AC3583');
        $this->addSql('ALTER TABLE operation_user DROP FOREIGN KEY FK_A2E38BD8A76ED395');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE operation_user');
        $this->addSql('DROP TABLE user');
    }
}

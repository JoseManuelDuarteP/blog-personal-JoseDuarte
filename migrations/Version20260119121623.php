<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260119121623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE like_post (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_83FFB0F3A76ED395 (user_id), INDEX IDX_83FFB0F34B89032C (post_id), PRIMARY KEY (user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, titulo VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, imagen VARCHAR(255) DEFAULT NULL, fecha_publicacion DATETIME NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE like_post ADD CONSTRAINT FK_83FFB0F3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE like_post ADD CONSTRAINT FK_83FFB0F34B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE like_post DROP FOREIGN KEY FK_83FFB0F3A76ED395');
        $this->addSql('ALTER TABLE like_post DROP FOREIGN KEY FK_83FFB0F34B89032C');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('DROP TABLE like_post');
        $this->addSql('DROP TABLE post');
    }
}

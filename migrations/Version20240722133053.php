<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240722133053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD carriers_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398893B6405 FOREIGN KEY (carriers_id) REFERENCES carrier (id)');
        $this->addSql('CREATE INDEX IDX_F5299398893B6405 ON `order` (carriers_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398893B6405');
        $this->addSql('DROP INDEX IDX_F5299398893B6405 ON `order`');
        $this->addSql('ALTER TABLE `order` DROP carriers_id');
    }
}

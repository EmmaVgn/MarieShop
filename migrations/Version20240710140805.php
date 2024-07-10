<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240710140805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advise DROP FOREIGN KEY FK_2E401CDCD44F05E5');
        $this->addSql('DROP INDEX UNIQ_2E401CDCD44F05E5 ON advise');
        $this->addSql('ALTER TABLE advise ADD image_path VARCHAR(255) DEFAULT NULL, DROP images_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE advise ADD images_id INT DEFAULT NULL, DROP image_path');
        $this->addSql('ALTER TABLE advise ADD CONSTRAINT FK_2E401CDCD44F05E5 FOREIGN KEY (images_id) REFERENCES images (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2E401CDCD44F05E5 ON advise (images_id)');
    }
}

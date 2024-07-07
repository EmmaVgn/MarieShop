<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240704164634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD botical_name VARCHAR(255) NOT NULL, ADD part_of_plant VARCHAR(255) NOT NULL, ADD extraction_method VARCHAR(255) NOT NULL, ADD culture VARCHAR(255) NOT NULL, ADD introduction LONGTEXT NOT NULL, ADD advise LONGTEXT NOT NULL, ADD precautions LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP botical_name, DROP part_of_plant, DROP extraction_method, DROP culture, DROP introduction, DROP advise, DROP precautions');
    }
}

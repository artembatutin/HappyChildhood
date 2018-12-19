<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181219203407 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP confirmed, CHANGE roles roles JSON NOT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(6) DEFAULT NULL, CHANGE province province VARCHAR(25) DEFAULT NULL, CHANGE phone phone INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD confirmed TINYINT(1) NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE postal_code postal_code VARCHAR(6) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE province province VARCHAR(25) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone INT DEFAULT NULL');
    }
}

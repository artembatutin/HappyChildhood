<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181219212827 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE child (id INT AUTO_INCREMENT NOT NULL, family_id_id INT NOT NULL, group_id_id INT NOT NULL, first_name VARCHAR(64) NOT NULL, last_name VARCHAR(64) NOT NULL, birth_date DATE NOT NULL, allergies LONGTEXT NOT NULL, medications LONGTEXT NOT NULL, INDEX IDX_22B3542943330D24 (family_id_id), INDEX IDX_22B354292F68B530 (group_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enrollment (id INT AUTO_INCREMENT NOT NULL, group_id_id INT NOT NULL, enrollment_hash VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, creation_date DATETIME NOT NULL, expired TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_DBDCD7E1C4F5AD6C (enrollment_hash), INDEX IDX_DBDCD7E12F68B530 (group_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, max_capacity SMALLINT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parent_family_link (id INT AUTO_INCREMENT NOT NULL, family_id_id INT NOT NULL, parent_id_id INT NOT NULL, INDEX IDX_29F729A543330D24 (family_id_id), INDEX IDX_29F729A5B3750AF4 (parent_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B3542943330D24 FOREIGN KEY (family_id_id) REFERENCES family (id)');
        $this->addSql('ALTER TABLE child ADD CONSTRAINT FK_22B354292F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E12F68B530 FOREIGN KEY (group_id_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE parent_family_link ADD CONSTRAINT FK_29F729A543330D24 FOREIGN KEY (family_id_id) REFERENCES family (id)');
        $this->addSql('ALTER TABLE parent_family_link ADD CONSTRAINT FK_29F729A5B3750AF4 FOREIGN KEY (parent_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(6) DEFAULT NULL, CHANGE province province VARCHAR(25) DEFAULT NULL, CHANGE phone phone INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE child DROP FOREIGN KEY FK_22B354292F68B530');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E12F68B530');
        $this->addSql('DROP TABLE child');
        $this->addSql('DROP TABLE enrollment');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE parent_family_link');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE postal_code postal_code VARCHAR(6) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE province province VARCHAR(25) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone INT DEFAULT NULL');
    }
}

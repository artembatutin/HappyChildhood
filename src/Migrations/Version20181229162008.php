<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181229162008 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, creation_date DATETIME NOT NULL, content LONGBLOB NOT NULL, visibility VARCHAR(14) NOT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_4DB9D91CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announcement_viewers (id INT AUTO_INCREMENT NOT NULL, announcement_id INT NOT NULL, child_group_id INT NOT NULL, INDEX IDX_FCAD6B16913AEA17 (announcement_id), INDEX IDX_FCAD6B167453A4E3 (child_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, announcement_id INT NOT NULL, user_id INT NOT NULL, creation_date DATETIME NOT NULL, blocked TINYINT(1) NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_9474526C913AEA17 (announcement_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE announcement_viewers ADD CONSTRAINT FK_FCAD6B16913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id)');
        $this->addSql('ALTER TABLE announcement_viewers ADD CONSTRAINT FK_FCAD6B167453A4E3 FOREIGN KEY (child_group_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `group` CHANGE max_capacity max_capacity SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(6) DEFAULT NULL, CHANGE province province VARCHAR(25) DEFAULT NULL, CHANGE phone phone INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE announcement_viewers DROP FOREIGN KEY FK_FCAD6B16913AEA17');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C913AEA17');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE announcement_viewers');
        $this->addSql('DROP TABLE comment');
        $this->addSql('ALTER TABLE `group` CHANGE max_capacity max_capacity SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE postal_code postal_code VARCHAR(6) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE province province VARCHAR(25) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone INT DEFAULT NULL');
    }
}

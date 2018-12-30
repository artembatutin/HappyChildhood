<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181229165809 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE inbox (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7E11F339A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_inbox_id INT NOT NULL, title VARCHAR(255) NOT NULL, message_file LONGBLOB NOT NULL, date_sent DATETIME NOT NULL, INDEX IDX_B6BD307F14E16423 (sender_inbox_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_receiver (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, receiver_inbox_id INT NOT NULL, read_flag TINYINT(1) NOT NULL, INDEX IDX_349E4887537A1329 (message_id), INDEX IDX_349E4887207F7228 (receiver_inbox_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inbox ADD CONSTRAINT FK_7E11F339A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F14E16423 FOREIGN KEY (sender_inbox_id) REFERENCES inbox (id)');
        $this->addSql('ALTER TABLE message_receiver ADD CONSTRAINT FK_349E4887537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE message_receiver ADD CONSTRAINT FK_349E4887207F7228 FOREIGN KEY (receiver_inbox_id) REFERENCES inbox (id)');
        $this->addSql('ALTER TABLE `group` CHANGE max_capacity max_capacity SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE postal_code postal_code VARCHAR(6) DEFAULT NULL, CHANGE province province VARCHAR(25) DEFAULT NULL, CHANGE phone phone INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F14E16423');
        $this->addSql('ALTER TABLE message_receiver DROP FOREIGN KEY FK_349E4887207F7228');
        $this->addSql('ALTER TABLE message_receiver DROP FOREIGN KEY FK_349E4887537A1329');
        $this->addSql('DROP TABLE inbox');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_receiver');
        $this->addSql('ALTER TABLE `group` CHANGE max_capacity max_capacity SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE postal_code postal_code VARCHAR(6) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE province province VARCHAR(25) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE phone phone INT DEFAULT NULL');
    }
}

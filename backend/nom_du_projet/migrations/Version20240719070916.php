<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719070916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_notifications (notification_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8E8E1D83EF1A9D84 (notification_id), INDEX IDX_8E8E1D83A76ED395 (user_id), PRIMARY KEY(notification_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_notifications ADD CONSTRAINT FK_8E8E1D83EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notifications ADD CONSTRAINT FK_8E8E1D83A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E9537A1329');
        $this->addSql('DROP INDEX IDX_8A8E26E9537A1329 ON conversation');
        $this->addSql('ALTER TABLE conversation ADD title VARCHAR(255) NOT NULL, DROP message_id');
        $this->addSql('ALTER TABLE message ADD group_id INT NOT NULL, DROP is_global');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FFE54D947 FOREIGN KEY (group_id) REFERENCES group_conversation (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FFE54D947 ON message (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_notifications DROP FOREIGN KEY FK_8E8E1D83EF1A9D84');
        $this->addSql('ALTER TABLE user_notifications DROP FOREIGN KEY FK_8E8E1D83A76ED395');
        $this->addSql('DROP TABLE user_notifications');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FFE54D947');
        $this->addSql('DROP INDEX IDX_B6BD307FFE54D947 ON message');
        $this->addSql('ALTER TABLE message ADD is_global TINYINT(1) NOT NULL, DROP group_id');
        $this->addSql('ALTER TABLE conversation ADD message_id INT NOT NULL, DROP title');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E9537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8A8E26E9537A1329 ON conversation (message_id)');
    }
}

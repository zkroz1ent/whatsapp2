<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718150936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_notification (user_id INT NOT NULL, notification_id INT NOT NULL, INDEX IDX_3F980AC8A76ED395 (user_id), INDEX IDX_3F980AC8EF1A9D84 (notification_id), PRIMARY KEY(user_id, notification_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notifications DROP FOREIGN KEY FK_8E8E1D83A76ED395');
        $this->addSql('ALTER TABLE user_notifications DROP FOREIGN KEY FK_8E8E1D83EF1A9D84');
        $this->addSql('DROP TABLE user_notifications');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_notifications (notification_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8E8E1D83A76ED395 (user_id), INDEX IDX_8E8E1D83EF1A9D84 (notification_id), PRIMARY KEY(notification_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_notifications ADD CONSTRAINT FK_8E8E1D83A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notifications ADD CONSTRAINT FK_8E8E1D83EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_notification DROP FOREIGN KEY FK_3F980AC8A76ED395');
        $this->addSql('ALTER TABLE user_notification DROP FOREIGN KEY FK_3F980AC8EF1A9D84');
        $this->addSql('DROP TABLE user_notification');
    }
}

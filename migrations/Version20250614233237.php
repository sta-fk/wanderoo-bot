<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614233237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE trip (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', user_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', title VARCHAR(255) DEFAULT NULL, start_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', end_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', currency VARCHAR(4) NOT NULL, created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', updated_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', INDEX IDX_7656F53BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trip_day (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', stop_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', day_index SMALLINT NOT NULL, activities JSON NOT NULL, food_places JSON NOT NULL, date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', updated_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', INDEX IDX_6969D02A3902063D (stop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trip_stop (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', trip_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', country_name VARCHAR(255) NOT NULL, city_name VARCHAR(255) NOT NULL, currency VARCHAR(4) NOT NULL, budget DOUBLE PRECISION NOT NULL, arrival_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', departure_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', interests JSON NOT NULL, trip_style VARCHAR(255) NOT NULL, local_transport VARCHAR(255) DEFAULT NULL, created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', updated_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', INDEX IDX_926E85DDA5BC2E0E (trip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', language VARCHAR(3) NOT NULL, chat_id INT NOT NULL, created_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', updated_at DATE NOT NULL COMMENT '(DC2Type:date_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip ADD CONSTRAINT FK_7656F53BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_day ADD CONSTRAINT FK_6969D02A3902063D FOREIGN KEY (stop_id) REFERENCES trip_stop (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_stop ADD CONSTRAINT FK_926E85DDA5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_day DROP FOREIGN KEY FK_6969D02A3902063D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE trip_stop DROP FOREIGN KEY FK_926E85DDA5BC2E0E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip_day
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip_stop
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}

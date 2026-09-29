<?php
/**
 * Version20260929124500.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260929124500 extends AbstractMigration
{
    /**
     * @var string
     */
    private const TABLE_NAME = 'exchange_rate_day';

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Create table ' . self::TABLE_NAME;
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema): void
    {
        $tableName = self::TABLE_NAME;
        $this->addSql(
            "CREATE TABLE {$tableName} (
                day_id INT AUTO_INCREMENT NOT NULL,
                rate_date DATE NOT NULL,
                source VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'cbr',
                status ENUM('pending', 'processing', 'completed', 'failed') NOT NULL,
                attempts INT NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE INDEX uniq_{$tableName}_date_source (rate_date, source),
                PRIMARY KEY (day_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB"
        );
        $this->addSql("
            ALTER TABLE {$tableName}
            MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            MODIFY updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");
        $this->addSql(
            "CREATE TRIGGER {$tableName}_before_insert
             BEFORE INSERT ON {$tableName}
             FOR EACH ROW
             SET NEW.source = LOWER(NEW.source)"
        );
        $this->addSql(
            "CREATE TRIGGER {$tableName}_before_update
             BEFORE UPDATE ON {$tableName}
             FOR EACH ROW
             SET NEW.source = LOWER(NEW.source)"
        );
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema): void
    {
        $tableName = self::TABLE_NAME;
        $this->addSql(
            "DROP TRIGGER IF EXISTS {$tableName}_before_insert"
        );
        $this->addSql(
            "DROP TRIGGER IF EXISTS {$tableName}_before_update"
        );
        $this->addSql(
            "DROP TABLE {$tableName}"
        );
    }
}

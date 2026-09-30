<?php
/**
 * Version20260929013502.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260929013502 extends AbstractMigration
{
    /**
     * @var string
     */
    private const TABLE_NAME = 'exchange_rate_data';

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
                rate_id INT AUTO_INCREMENT NOT NULL,
                rate_date DATE NOT NULL,
                source VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'cbr',
                base_currency VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                quote_currency VARCHAR(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                rate_value NUMERIC(40, 20) NOT NULL,
                created_at DATETIME NOT NULL,
                UNIQUE INDEX uniq_{$tableName}_dt_sour_cur (rate_date, source, base_currency, quote_currency),
                PRIMARY KEY (rate_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB"
        );
        $this->addSql("
            ALTER TABLE {$tableName}
            MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ");
        $this->addSql(
            "CREATE TRIGGER {$tableName}_before_insert
             BEFORE INSERT ON {$tableName}
             FOR EACH ROW
             SET NEW.base_currency = UPPER(NEW.base_currency), 
             NEW.quote_currency = UPPER(NEW.quote_currency),
             NEW.source = LOWER(NEW.source)"
        );
        $this->addSql(
            "CREATE TRIGGER {$tableName}_before_update
             BEFORE UPDATE ON {$tableName}
             FOR EACH ROW
             SET NEW.base_currency = UPPER(NEW.base_currency), 
             NEW.quote_currency = UPPER(NEW.quote_currency),
             NEW.source = LOWER(NEW.source)"
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

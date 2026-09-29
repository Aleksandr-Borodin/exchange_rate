<?php
/**
 * DatabaseConfig.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

final class DatabaseConfig
{
    /**
     * @return array
     */
    public static function fromEnv(): array
    {
        return [
            'driver' => 'pdo_mysql',
            'host' => $_ENV['DB_HOST'],
            'port' => (int) $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => 'utf8mb4',
        ];
    }
}

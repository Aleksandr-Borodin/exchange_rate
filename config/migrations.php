<?php
/**
 * migrations.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;

$entityManager = require __DIR__ . '/doctrine.php';

return DependencyFactory::fromEntityManager(
    new \Doctrine\Migrations\Configuration\Migration\ConfigurationArray([
        'migrations_paths' => [
            'ExchangeRate\Migrations' => __DIR__ . '/../migrations',
        ],
        'table_storage' => [
            'table_name' => 'doctrine_migration_versions',
        ],
    ]),
    new ExistingEntityManager($entityManager)
);

<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseContext implements Context
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @BeforeScenario
     */
    public function clearDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        
        if ($platform instanceof \Doctrine\DBAL\Platforms\SqlitePlatform) {
            // SQLite
            $connection->executeStatement('PRAGMA foreign_keys = OFF');
            
            $tables = $connection->createSchemaManager()->listTableNames();
            foreach ($tables as $tableName) {
                $connection->executeStatement("DELETE FROM `{$tableName}`");
            }
            
            $connection->executeStatement('PRAGMA foreign_keys = ON');
        } else {
            // MySQL/MariaDB
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
            
            $tables = $connection->createSchemaManager()->listTableNames();
            foreach ($tables as $tableName) {
                $connection->executeStatement("TRUNCATE TABLE `{$tableName}`");
            }
            
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
        }
        
        $this->entityManager->clear();
    }

    /**
     * @Given que la base de données est vide
     * @Given la base de données est vide
     */
    public function quelaBaseDeDonneesEstVide(): void
    {
        $this->clearDatabase();
    }
}

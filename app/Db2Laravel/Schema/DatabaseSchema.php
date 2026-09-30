<?php

namespace App\Db2Laravel\Schema;

final class DatabaseSchema
{
    public function __construct(
        public readonly string $connectionName,
        public readonly string $databaseName,
        public readonly string $tablePrefix,
        public readonly array $tables = [],
    ) {
    }
}

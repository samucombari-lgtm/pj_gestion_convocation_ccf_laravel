<?php

namespace App\Db2Laravel\Schema;

final class TableSchema
{
    public function __construct(
        public readonly string $physicalName,
        public readonly string $name,
        public readonly array $columns = [],
        public readonly array $indexes = [],
        public readonly array $foreignKeys = [],
    ) {
    }
}

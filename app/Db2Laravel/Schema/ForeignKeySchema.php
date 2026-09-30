<?php

namespace App\Db2Laravel\Schema;

final class ForeignKeySchema
{
    public function __construct(
        public readonly string $name,
        public readonly array $columns,
        public readonly string $foreignPhysicalTable,
        public readonly string $foreignTable,
        public readonly array $foreignColumns,
        public readonly ?string $onUpdate = null,
        public readonly ?string $onDelete = null,
    ) {
    }
}

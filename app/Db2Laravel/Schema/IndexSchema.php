<?php

namespace App\Db2Laravel\Schema;

final class IndexSchema
{
    public function __construct(
        public readonly string $name,
        public readonly array $columns,
        public readonly bool $unique,
        public readonly bool $primary,
    ) {
    }
}
